Rbac
====

The Rbac class is provided to allow permission matching based on request params and a set of rules.
This class was extracted initially from SimpleRbacAuthorize to allow using it from Middlewares.
Along with the original extraction, new classes for permission providers were created too, to allow
using other sources to provide the permissions array, for example the database or a cache engine. 


Setup
-----

Create an instance of the Rbac class and use `checkPermissions` method
```php
    $rbac = new Rbac();
    $isAuthorized = $this->rbac->checkPermissions($userData, $request);
```

You can define the actual permissions array in the constructor options (key `permissions`), 
or specify how the permissions are going to be loaded, by default via 
configuration file.

Extend `AbstractProvider` class and use the `permissions_provider_class` config key to let
the Rbac use your own permissions provider, for example to read the permissions from the database.

Permission rules syntax
-----------------

* Permissions are evaluated top-down, first matching permission will apply
* Each permission is an associative array of rules with following structure: `'value_to_check' => 'expected_value'`
* `value_to_check` can be any key from user array or one of special keys:
    * Routing params:
        * `prefix`
        * `plugin`
        * `extension`
        * `controller`
        * `action`
    * `role` - Alias/shortcut to field defined in `role_field` config value
    * `allowed` - Must be placed at the end of the rule array, see below
    * `bypassAuth` - Must be placed at the end of the rule array, see below
* If you have a user field that overlaps with special keys (eg. `$user->allowed`) you can prepend `user.` to key to force matching from user array (eg. `user.allowed`)
* The keys can be placed in any order with exception of `allowed` or `bypassAuth` which must be last one (see below)
* `value_to_check` can be prepended with `*` to match everything except `expected_value`
* `expected_value` can be one of following things:
    * `*` will match absolutely everything
    * A _string_/_integer_/_boolean_/etc - will match only the specified value
    * An _array_ of strings/integers/booleans/etc (can be mixed). The rule will match if real value is equal to any of expected ones
    * A callable/object (see below)
* If any of rules fail, the permission is discarded and the next one is evaluated
* A very special key `allowed` exists which has the following behaviour:
    * If `expected_value` is a callable/object then it's executed and the result is casted to boolean
    * If `expected_value` is **not** a callable/object then it's simply casted to boolean
    * The `*` is checked and if found the result is inverted
    * The final boolean value is **the result of permission** checker. This means if it is `false` then no other permissions are checked and the user is denied access. And if it is `true`, you need at least an user identified, or it'll deny your access attempt.
    For this reason the `allowed` key must be placed at the end of permission since no other rules are executed after it
* ONLY when using Rbac within the Middleware implementation: Another special key is `bypassAuth`, if set to true will allow the request, even if there is no user data. Note this won't work with the regular AuthComponent setup, and you'll require using `AuthComponent::allow` method to specify public actions. 

**Notes**:

* For Superadmin access (permission to access ALL THE THINGS in your app) there is a specific Authorize Object provided
* Permissions that do not have `controller` and/or `action` keys (or the inverted versions) are automatically discarded in order to prevent errors.
* If you need to match all controllers/actions you should explicitly do `'contoller' => '*'`
* Key `user` (or the inverted version) is illegal (as it's impossible to match an array) and any permission containing it will be discarded
* If the permission is discarded for the reasons stated above, a debug message will be logged

### Permission Callbacks: 

You could use a callback in your 'allowed' to process complex authentication, like
  - ownership
  - permissions stored in your database
  - permission based on an external service API call

Example *ownership* callback, to allow users to edit their own Posts:

```php
    'allowed' => function (array $user, $role, \Cake\Http\ServerRequest $request) {
        $postId = \Cake\Utility\Hash::get($request->params, 'pass.0');
        $post = \Cake\ORM\TableRegistry::get('Posts')->get($postId);
        $userId = $user['id'];
        if (!empty($post->user_id) && !empty($userId)) {
            return $post->user_id === $userId;
        }
        return false;
    }
```

### Permission Rules: 

If you see that you are duplicating logic in your callbacks, you can create rule class to re-use the logic.
For example, the above ownership callback is included in CakeDC\Users as `Owner` rule
```php
'allowed' => new \CakeDC\Auth\Rbac\Rules\Owner() //will pick by default the post id from the first pass param
```
Check the [Owner Rule](OwnerRule.md) documentation for more details

## Creating rule classes

The only requirement is to implement `\CakeDC\Auth\Rbac\Rules\Rule` interface which has one method:

```php
class YourRule implements \CakeDC\Auth\Rbac\Rules\Rule
{
    /**
     * Check the current entity is owned by the logged in user
     *
     * @param array $user Auth array with the logged in data
     * @param string $role role of the user
     * @param Request $request current request, used to get a default table if not provided
     * @return bool
     */
    public function allowed(array $user, $role, Request $request)
    {
        // Your logic here
    }
}
```

This logic can be anything: database, external auth, etc.

Also, if you are using DB, you can choose to extend `\CakeDC\Auth\Rbac\Rules\AbstractRule` since it provides convenience methods for reading from DB

## RuleRegistry

If you have a large application with many endpoints you'll possibly reuse the same rule in many of them.
Instead of using `new` to create a new rule for every permission defined you can use the RuleRegistry class

```php
'allowed' => RuleRegistry::get(Owner::class), //will reuse the same Owner instance across your application 
```
