Decoupling decisions and compatibility
======================================

In order to decouple this package from `TYPO3.Flow` a few necessary but implication-filled decisions had to be made.

No more dependency injection
----------------------------

It is simply gone and will not be implemented again in any shape or form. The DI concept from `TYPO3.Flow` does not apply on a
wider scale and it is every bit as possible to achieve the same results through the constructor methods. While DI is a nice
concept for usability, it is better to sacrifice it for more universal class support.

No more widget support
----------------------

It is also gone and will not be implemented again. However: because of the nature of Fluid and ViewHelpers, **recreating the
implementation that behaves like Widgets but does so in the context of the framework in which it gets used** is the correct way
to achieve this behavior.

As an added benefit, all translated labels and a **lot** of functional tests and fixtures can and have been removed.

ViewHelpers can't use render method arguments
---------------------------------------------

Although still technically feasible to implement, a decision was made to sacrifice the support for `render()` method arguments in
ViewHelpers (as opposed to using `registerArgument` inside `initializeArguments`). The following reasons are given:

1. Render method type hinting beyond strict types is impossible without analysing phpdoc comments which implies Reflection as
   well as a dependency on the `TYPO3.Flow` annotation parser.
2. Performance and testability is exactly the same.
3. A single method to register arguments now exists and `ArgumentDefinition` can be reduced in complexity.
4. Having only one method to check in every instance means better performance overall.

Overriding these capabilites is possible and implies a custom `ViewHelperInvoker` returned from a custom `ViewHelperResolver`.

The View consumes TemplatePaths
-------------------------------

Rather than allow the View class to be aware of the Request via the ControllerContext, both the Request and ControllerContext
concepts have been completely removed. A new strategy has taken the place of these: the TemplatePaths.

In order to instantiate a View instance, a special TemplatePaths instance must now be passed as first argument. This TemplatePaths
instance *is then responsible for all the resolving and retrieval of template files*. This means that replacing the TemplatePaths
instance that gets used will allow another implementation to **effectively change how Fluid resolves template files**.

This also means that the View itself no longer has any file resolving capabilities. Instead, it relies on the provided instance
to resolve every file and template source. For usability, there are dedicated methods to set template path and filename as well
as layout path and filename.

**Because of this the path treatments are now completely decoupled and no longer uses expression expansions to create arrays of
expected paths**. Instead, each path is iterated and the epected filename checked and returned if found. In order to potentially
re-implement this decoupled package into `TYPO3.Flow` as well as `TYPO3.CMS` this means that these paths can then be generated by
the MVC and simply passed along to the View - or, íf more complex behavior is required, a custom TemplatePaths implementation
can be created.

As a side effect of this, **the View no longer supports any options**. Where before a user was able to pass paths, filenames
and other settings as `options` on the View, such behavior is no longer required due to the use of TemplatePaths. However, such
behavior can be restored by subclassing the TemplateView and adding `options` support which simply delegates to TemplatePaths to
change root paths etc.

Obviously your paths can no longer use any custom syntax or markers for replacement such as `EXT:...` paths or `@marker` patterns.
To use such expressions make sure you convert the paths *before* you pass them to the View; e.g. do so inside your custom
TemplatePaths or before you pass the paths to the built-in TemplatePaths object.

Deprecated code has been evicted
--------------------------------

This includes all and every support for the legacy path name conventions - only arrays of paths are now accepted except when
specifying the full template- or layout path and filename on the View.

Note about injecting functionality
----------------------------------

It is possible to restore almost all of the removed features by creating custom implementations of the following classes:

* https://github.com/TYPO3Fluid/Fluid/blob/master/src/View/TemplatePaths.php to change how template files are resolved.
* https://github.com/TYPO3Fluid/Fluid/blob/master/src/Core/ViewHelper/ViewHelperResolver.php to change how each
  ViewHelper is resolved, how its arguments are retrieved, which namespaces are available and expected class names of ViewHelpers.

When combined in a package that implements `TYPO3.Fluid` in a framework, these two classes together make it possible to change
all the behaviors that were changed to make `TYPO3.Fluid` more portable. The solutions are, in order:

1. Dependency injection in ViewHelpers can be restored by overriding the `createViewHelperClassInstance` method of the
   ViewHelperResolver implementation. The View itself can of course also be loaded by an object manager that does injection.
2. Widgets can be reintroduced as a framework-specific feature by making the ViewHelperResolver return the desired class names
   when the TemplateParser tries to load the ViewHelpers that were removed. This includes all of the other ViewHelpers which were
   sacrificed for portability because they had too strong dependencies on the framework; for example the `f:form` helpers known
   from TYPO3 CMS and Flow.
3. The arguments of each ViewHelper can be adjusted - or the class itself can be replaced. This allows the framework to replace
   those ViewHelpers that have been "dumbed down" (for example the debug ViewHelper which in CMS or Flow would use the
   DebuggerUtility to display variables but in this standalone version uses a plain `var_dump`).
4. The TemplatePaths implementation can be replaced by one that supports the necessary folder structures and path treatments, for
   example allowing the `EXT:....` syntax in paths and making it possible to "bubble sub package" or whatever special feature the
   template file resolving should support.
5. Deprecated code and support for legacy class names can be introduced by overriding the ViewHelperResolver. The aliases can be
   defined any way desired - as long as the ViewHelperResolver is aware of them and will return the **new** class as replacement.
6. The way the ViewHelpers are themselves executed can be overridden via a custom ViewHelperInvoker returned from the custom
   ViewHelperResolver. Overriding this part allows, among other things, restoring the `render()` method argument support.