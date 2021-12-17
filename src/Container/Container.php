<?php

declare(strict_types=1);

namespace Sura\Container;

use ArrayAccess;
use Closure;
use Exception;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Sura\Exception\SuraException;

/**
 *
 */
class Container implements ArrayAccess
{
	/**
	 * The current globally available container (if any).
	 *
	 * @var static
	 */
	protected static Container $instance;
	
	/**
	 * An array of the types that have been resolved.
	 *
	 * @var bool[]
	 */
	protected array $resolved = [];
	
	/**
	 * The container's bindings.
	 *
	 * @var array[]
	 */
	protected array $bindings = [];
	
	/**
	 * The container's method bindings.
	 *
	 * @var \Closure[]
	 */
	protected array $methodBindings = [];
	
	/**
	 * The container's shared instances.
	 *
	 * @var object[]
	 */
	protected array $instances = [];
	
	/**
	 * The registered type aliases.
	 *
	 * @var string[]
	 */
	protected array $aliases = [];
	
	/**
	 * The registered aliases keyed by the abstract name.
	 *
	 * @var array[]
	 */
	protected array $abstractAliases = [];
	
	/**
	 * The extension closures for services.
	 *
	 * @var array[]
	 */
	protected array $extenders = [];
	
	/**
	 * All of the registered tags.
	 *
	 * @var array[]
	 */
	protected array $tags = [];
	
	/**
	 * The stack of concretions currently being built.
	 *
	 * @var array[]
	 */
	protected array $buildStack = [];
	
	/**
	 * The parameter override stack.
	 *
	 * @var array[]
	 */
	protected array $with = [];
	
	/**
	 * The contextual binding map.
	 *
	 * @var array[]
	 */
	public array $contextual = [];
	
	/**
	 * All of the registered rebound callbacks.
	 *
	 * @var array[]
	 */
	protected array $reboundCallbacks = [];
	
	/**
	 * All of the global before resolving callbacks.
	 *
	 * @var \Closure[]
	 */
	protected array $globalBeforeResolvingCallbacks = [];
	
	/**
	 * All of the global resolving callbacks.
	 *
	 * @var \Closure[]
	 */
	protected array $globalResolvingCallbacks = [];
	
	/**
	 * All of the global after resolving callbacks.
	 *
	 * @var \Closure[]
	 */
	protected array $globalAfterResolvingCallbacks = [];
	
	/**
	 * All of the before resolving callbacks by class type.
	 *
	 * @var array[]
	 */
	protected array $beforeResolvingCallbacks = [];
	
	/**
	 * All of the resolving callbacks by class type.
	 *
	 * @var array[]
	 */
	protected array $resolvingCallbacks = [];
	
	/**
	 * All of the after resolving callbacks by class type.
	 *
	 * @var array[]
	 */
	protected array $afterResolvingCallbacks = [];
	
	/**
	 * Define a contextual binding.
	 *
	 * @param array|string $concrete
	 */
	public function when(array|string $concrete)
	{
//		$aliases = [];
		
		foreach (Util::arrayWrap($concrete) as $c) {
			$aliases[] = $this->getAlias($c);
		}
		throw SuraException::error('err');
//        return new ContextualBindingBuilder($this, $aliases);
	}
	
	/**
	 * Determine if the given abstract type has been bound.
	 *
	 * @param string $abstract
	 * @return bool
	 */
	public function bound(string $abstract): bool
    {
		return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]) || $this->isAlias($abstract);
	}
	
	/**
	 *
	 */
	public function has($id): bool
    {
		return $this->bound($id);
	}
	
	/**
	 * Determine if the given abstract type has been resolved.
	 *
	 * @param string $abstract
	 * @return bool
	 */
	public function resolved(string $abstract): bool
    {
		if ($this->isAlias($abstract)) {
			$abstract = $this->getAlias($abstract);
		}
		
		return isset($this->resolved[$abstract]) || isset($this->instances[$abstract]);
	}
	
	/**
	 * Determine if a given type is shared.
	 *
	 * @param string $abstract
	 * @return bool
	 */
	public function isShared(string $abstract): bool
    {
		return isset($this->instances[$abstract]) || (isset($this->bindings[$abstract]['shared']) && $this->bindings[$abstract]['shared'] === true);
	}
	
	/**
	 * Determine if a given string is an alias.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function isAlias(string $name): bool
    {
		return isset($this->aliases[$name]);
	}
	
	/**
	 * Register a binding with the container.
	 *
	 * @param string $abstract
	 * @param \Closure|string|null $concrete
	 * @param bool $shared
	 * @return void
	 */
	public function bind(string $abstract, $concrete = null, $shared = false)
	{
		$this->dropStaleInstances($abstract);
		
		// If no concrete type was given, we will simply set the concrete type to the
		// abstract type. After that, the concrete type to be registered as shared
		// without being forced to state their classes in both of the parameters.
		if (is_null($concrete)) {
			$concrete = $abstract;
		}
		
		// If the factory is not a Closure, it means it is just a class name which is
		// bound into this container to the abstract type and we will just wrap it
		// up inside its own Closure to give us more convenience when extending.
		if (!$concrete instanceof Closure) {
			if (!is_string($concrete)) {
				throw SuraException::error(self::class . '::bind(): Argument #2 ($concrete) must be of type Closure|string|null');
			}
			
			$concrete = $this->getClosure($abstract, $concrete);
		}
		
		$this->bindings[$abstract] = compact('concrete', 'shared');
		
		// If the abstract type was already resolved in this container we'll fire the
		// rebound listener so that any objects which have already gotten resolved
		// can have their copy of the object updated via the listener callbacks.
		if ($this->resolved($abstract)) {
			$this->rebound($abstract);
		}
	}
	
	/**
	 * Get the Closure to be used when building a type.
	 *
	 * @param string $abstract
	 * @param string $concrete
	 * @return \Closure
	 */
	protected function getClosure(string $abstract, string $concrete): Closure
    {
		return function ($container, $parameters = []) use ($abstract, $concrete) {
			if ($abstract == $concrete) {
				return $container->build($concrete);
			}
			
			return $container->resolve($concrete, $parameters, $raiseEvents = false);
		};
	}
	
	/**
	 * Determine if the container has a method binding.
	 *
	 * @param string $method
	 * @return bool
	 */
	public function hasMethodBinding(string $method): bool
    {
		return isset($this->methodBindings[$method]);
	}
	
	/**
	 * Bind a callback to resolve with Container::call.
	 *
	 * @param array|string $method
	 * @param \Closure $callback
	 * @return void
	 */
	public function bindMethod(array|string $method, Closure $callback)
	{
		$this->methodBindings[$this->parseBindMethod($method)] = $callback;
	}

    /**
     * Get the method to be bound in class@method format.
     *
     * @param array|string $method
     * @return array|string
     */
	protected function parseBindMethod(array|string $method): array|string
    {
		if (is_array($method)) {
			return $method[0] . '@' . $method[1];
		}
		
		return $method;
	}
	
	/**
	 * Get the method binding for the given method.
	 *
	 * @param string $method
	 * @param mixed $instance
	 * @return mixed
	 */
	public function callMethodBinding(string $method, mixed $instance): mixed
    {
		return call_user_func($this->methodBindings[$method], $instance, $this);
	}
	
	/**
	 * Add a contextual binding to the container.
	 *
	 * @param string $concrete
	 * @param string $abstract
	 * @param string|\Closure $implementation
	 * @return void
	 */
	public function addContextualBinding(string $concrete, string $abstract, string|Closure $implementation)
	{
		$this->contextual[$concrete][$this->getAlias($abstract)] = $implementation;
	}
	
	/**
	 * Register a binding if it hasn't already been registered.
	 *
	 * @param string $abstract
	 * @param \Closure|string|null $concrete
	 * @param bool $shared
	 * @return void
	 */
	public function bindIf(string $abstract, Closure|string $concrete = null, $shared = false)
	{
		if (!$this->bound($abstract)) {
			$this->bind($abstract, $concrete, $shared);
		}
	}
	
	/**
	 * Register a shared binding in the container.
	 *
	 * @param string $abstract
	 * @param \Closure|string|null $concrete
	 * @return void
	 */
	public function singleton(string $abstract, $concrete = null)
	{
		$this->bind($abstract, $concrete, true);
	}
	
	/**
	 * Register a shared binding if it hasn't already been registered.
	 *
	 * @param string $abstract
	 * @param \Closure|string|null $concrete
	 * @return void
	 */
	public function singletonIf(string $abstract, $concrete = null)
	{
		if (!$this->bound($abstract)) {
			$this->singleton($abstract, $concrete);
		}
	}
	
	/**
	 * "Extend" an abstract type in the container.
	 *
	 * @param string $abstract
	 * @param \Closure $closure
	 * @return void
	 *
	 */
	public function extend(string $abstract, Closure $closure)
	{
		$abstract = $this->getAlias($abstract);
		
		if (isset($this->instances[$abstract])) {
			$this->instances[$abstract] = $closure($this->instances[$abstract], $this);
			
			$this->rebound($abstract);
		} else {
			$this->extenders[$abstract][] = $closure;
			
			if ($this->resolved($abstract)) {
				$this->rebound($abstract);
			}
		}
	}
	
	/**
	 * Register an existing instance as shared in the container.
	 *
	 * @param string $abstract
	 * @param mixed $instance
	 * @return mixed
	 */
	public function instance(string $abstract, mixed $instance): mixed
    {
		$this->removeAbstractAlias($abstract);
		
		$isBound = $this->bound($abstract);
		
		unset($this->aliases[$abstract]);
		
		// We'll check to determine if this type has been bound before, and if it has
		// we will fire the rebound callbacks registered with the container and it
		// can be updated with consuming classes that have gotten resolved here.
		$this->instances[$abstract] = $instance;
		
		if ($isBound) {
			$this->rebound($abstract);
		}
		
		return $instance;
	}
	
	/**
	 * Remove an alias from the contextual binding alias cache.
	 *
	 * @param string $searched
	 * @return void
	 */
	protected function removeAbstractAlias(string $searched)
	{
		if (!isset($this->aliases[$searched])) {
			return;
		}
		
		foreach ($this->abstractAliases as $abstract => $aliases) {
			foreach ($aliases as $index => $alias) {
				if ($alias == $searched) {
					unset($this->abstractAliases[$abstract][$index]);
				}
			}
		}
	}
	
	/**
	 * Assign a set of tags to a given binding.
	 *
	 * @param array|string $abstracts
	 * @param mixed|array ...$tags
	 * @return void
	 */
	public function tag(array|string $abstracts, mixed $tags)
	{
		$tags = is_array($tags) ? $tags : array_slice(func_get_args(), 1);
		
		foreach ($tags as $tag) {
			if (!isset($this->tags[$tag])) {
				$this->tags[$tag] = [];
			}
			
			foreach ((array)$abstracts as $abstract) {
				$this->tags[$tag][] = $abstract;
			}
		}
	}
	
	/**
	 * Resolve all of the bindings for a given tag.
	 *
	 * @param string $tag
	 * @return iterable
	 */
	public function tagged($tag)
	{
		if (!isset($this->tags[$tag])) {
			return [];
		}
		
		return new RewindableGenerator(function () use ($tag) {
			foreach ($this->tags[$tag] as $abstract) {
				yield $this->make($abstract);
			}
		}, count($this->tags[$tag]));
	}
	
	/**
	 * Alias a type to a different name.
	 *
	 * @param string $abstract
	 * @param string $alias
	 * @return void
	 *
	 */
	public function alias(string $abstract, string $alias)
	{
		if ($alias === $abstract) {
			throw SuraException::error("[{$abstract}] is aliased to itself.");
		}
		
		$this->aliases[$alias] = $abstract;
		
		$this->abstractAliases[$abstract][] = $alias;
	}
	
	/**
	 * Bind a new callback to an abstract's rebind event.
	 *
	 * @param string $abstract
	 * @param \Closure $callback
	 * @return mixed
	 */
	public function rebinding(string $abstract, Closure $callback): mixed
    {
		$this->reboundCallbacks[$abstract = $this->getAlias($abstract)][] = $callback;
		
		if ($this->bound($abstract)) {
			return $this->make($abstract);
		}else
            //todo update
            return false;

	}
	
	/**
	 * Refresh an instance on the given target and method.
	 *
	 * @param string $abstract
	 * @param mixed $target
	 * @param string $method
	 * @return mixed
	 */
	public function refresh(string $abstract, mixed $target, string $method): mixed
    {
		return $this->rebinding($abstract, function ($app, $instance) use ($target, $method) {
			$target->{$method}($instance);
		});
	}
	
	/**
	 * Fire the "rebound" callbacks for the given abstract type.
	 *
	 * @param string $abstract
	 * @return void
	 */
	protected function rebound(string $abstract)
	{
		$instance = $this->make($abstract);
		
		foreach ($this->getReboundCallbacks($abstract) as $callback) {
			call_user_func($callback, $this, $instance);
		}
	}
	
	/**
	 * Get the rebound callbacks for a given type.
	 *
	 * @param string $abstract
	 * @return array
	 */
	protected function getReboundCallbacks(string $abstract): array
    {
		return $this->reboundCallbacks[$abstract] ?? [];
	}
	
	/**
	 * Wrap the given closure such that its dependencies will be injected when executed.
	 *
	 * @param \Closure $callback
	 * @param array $parameters
	 * @return \Closure
	 */
	public function wrap(Closure $callback, array $parameters = []): Closure
    {
		return function () use ($callback, $parameters) {
			return $this->call($callback, $parameters);
		};
	}
	
	/**
	 * Call the given Closure / class@method and inject its dependencies.
	 *
	 * @param callable|string $callback
	 * @param array<string, mixed> $parameters
	 * @param string|null $defaultMethod
	 * @return mixed
	 *
	 */
	public function call($callback, array $parameters = [], $defaultMethod = null): mixed
    {
        //fixme
		return BoundMethod::call($this, $callback, $parameters, $defaultMethod);
	}
	
	/**
	 * Get a closure to resolve the given type from the container.
	 *
	 * @param string $abstract
	 * @return \Closure
	 */
	public function factory(string $abstract): Closure
    {
		return function () use ($abstract) {
			return $this->make($abstract);
		};
	}
	
	/**
	 * An alias function name for make().
	 *
	 * @param callable|string $abstract
	 * @param array $parameters
	 * @return mixed
	 *
	 */
	public function makeWith(callable|string $abstract, array $parameters = []): mixed
    {
		return $this->make($abstract, $parameters);
	}
	
	/**
	 * Resolve the given type from the container.
	 *
	 * @param callable|string $abstract
	 * @param array $parameters
	 * @return mixed
	 *
	 */
	public function make(callable|string $abstract, array $parameters = []): mixed
    {
		return $this->resolve($abstract, $parameters);
	}

    /**
     *  {}
     * @throws Exception
     */
	public function get($id)
	{
		try {
			return $this->resolve($id);
		} catch (Exception $e) {
			if ($this->has($id)) {
				throw $e;
			}
			
			throw SuraException::error('err get' . $id);

//            throw new EntryNotFoundException($id, $e->getCode(), $e);
		}
	}
	
	/**
	 * Resolve the given type from the container.
	 *
	 * @param callable|string $abstract
	 * @param array $parameters
	 * @param bool $raiseEvents
	 * @return mixed
	 *
	 */
	protected function resolve(callable|string $abstract, array $parameters = [], bool $raiseEvents = true): mixed
    {
		$abstract = $this->getAlias($abstract);
		
		// First we'll fire any event handlers which handle the "before" resolving of
		// specific types. This gives some hooks the chance to add various extends
		// calls to change the resolution of objects that they're interested in.
		if ($raiseEvents) {
			$this->fireBeforeResolvingCallbacks($abstract, $parameters);
		}
		
		$concrete = $this->getContextualConcrete($abstract);
		
		$needsContextualBuild = !empty($parameters) || !is_null($concrete);
		
		// If an instance of the type is currently being managed as a singleton we'll
		// just return an existing instance instead of instantiating new instances
		// so the developer can keep using the same objects instance every time.
		if (isset($this->instances[$abstract]) && !$needsContextualBuild) {
			return $this->instances[$abstract];
		}
		
		$this->with[] = $parameters;
		
		if (is_null($concrete)) {
			$concrete = $this->getConcrete($abstract);
		}
		
		// We're ready to instantiate an instance of the concrete type registered for
		// the binding. This will instantiate the types, as well as resolve any of
		// its "nested" dependencies recursively until all have gotten resolved.
		if ($this->isBuildable($concrete, $abstract)) {
			$object = $this->build($concrete);
		} else {
			$object = $this->make($concrete);
		}
		
		// If we defined any extenders for this type, we'll need to spin through them
		// and apply them to the object being built. This allows for the extension
		// of services, such as changing configuration or decorating the object.
		foreach ($this->getExtenders($abstract) as $extender) {
			$object = $extender($object, $this);
		}
		
		// If the requested type is registered as a singleton we'll want to cache off
		// the instances in "memory" so we can return it later without creating an
		// entirely new instance of an object on each subsequent request for it.
		if ($this->isShared($abstract) && !$needsContextualBuild) {
			$this->instances[$abstract] = $object;
		}
		
		if ($raiseEvents) {
			$this->fireResolvingCallbacks($abstract, $object);
		}
		
		// Before returning, we will also set the resolved flag to "true" and pop off
		// the parameter overrides for this build. After those two things are done
		// we will be ready to return back the fully constructed class instance.
		$this->resolved[$abstract] = true;
		
		array_pop($this->with);
		
		return $object;
	}
	
	/**
	 * Get the concrete type for a given abstract.
	 *
	 * @param callable|string $abstract
	 * @return mixed
	 */
	protected function getConcrete(callable|string $abstract): mixed
    {
		// If we don't have a registered resolver or concrete for the type, we'll just
		// assume each type is a concrete name and will attempt to resolve it as is
		// since the container should be able to resolve concretes automatically.
		if (isset($this->bindings[$abstract])) {
			return $this->bindings[$abstract]['concrete'];
		}
		
		return $abstract;
	}
	
	/**
	 * Get the contextual concrete binding for the given abstract.
	 *
	 * @param callable|string $abstract
	 * @return \Closure|string|null
	 */
	protected function getContextualConcrete(callable|string $abstract): string|Closure|null
    {
		if (!is_null($binding = $this->findInContextualBindings($abstract))) {
			return $binding;
		}
		
		// Next we need to see if a contextual binding might be bound under an alias of the
		// given abstract type. So, we will need to check if any aliases exist with this
		// type and then spin through them and check for contextual bindings on these.
		if (empty($this->abstractAliases[$abstract])) {
			return null;
		}
		
		foreach ($this->abstractAliases[$abstract] as $alias) {
			if (!is_null($binding = $this->findInContextualBindings($alias))) {
				return $binding;
			}
		}
        return null;
	}
	
	/**
	 * Find the concrete binding for the given abstract in the contextual binding array.
	 *
	 * @param callable|string $abstract
	 * @return \Closure|string|null
	 */
	protected function findInContextualBindings(callable|string $abstract): string|Closure|null
    {
		return $this->contextual[end($this->buildStack)][$abstract] ?? null;
	}
	
	/**
	 * Determine if the given concrete is buildable.
	 *
	 * @param mixed $concrete
	 * @param string $abstract
	 * @return bool
	 */
	protected function isBuildable(mixed $concrete, string $abstract): bool
    {
		return $concrete === $abstract || $concrete instanceof Closure;
	}

    /**
     * Instantiate a concrete instance of the given type.
     *
     * @param string|Closure $concrete
     * @return mixed
     * @throws ReflectionException
     */
	public function build(string|Closure $concrete): mixed
	{
		// If the concrete type is actually a Closure, we will just execute it and
		// hand back the results of the functions, which allows functions to be
		// used as resolvers for more fine-tuned resolution of these objects.
		if ($concrete instanceof Closure) {
			return $concrete($this, $this->getLastParameterOverride());
		}
		
		try {
			$reflector = new ReflectionClass($concrete);
		} catch (ReflectionException $e) {
//            throw new BindingResolutionException("Target class [$concrete] does not exist.", 0, $e);
//            throw SuraException::Error("err");
//            throw new SuraException::err();
			exit('err');
		}
		
		// If the type is not instantiable, the developer is attempting to resolve
		// an abstract type such as an Interface or Abstract Class and there is
		// no binding registered for the abstractions so we need to bail out.
		if (!$reflector->isInstantiable()) {
            //fixme
			return $this->notInstantiable($concrete);
		}
		
		$this->buildStack[] = $concrete;
		
		$constructor = $reflector->getConstructor();
		
		// If there are no constructors, that means there are no dependencies then
		// we can just resolve the instances of the objects right away, without
		// resolving any other types or dependencies out of these containers.
		if (is_null($constructor)) {
			array_pop($this->buildStack);
			
			return new $concrete();
		}
		
		$dependencies = $constructor->getParameters();
		
		// Once we have all the constructor's parameters we can create each of the
		// dependency instances and then use the reflection instances to make a
		// new instance of this class, injecting the created dependencies in.

        //fixme
		try {
			$instances = $this->resolveDependencies($dependencies);
		} catch (BindingResolutionException $e) {
			array_pop($this->buildStack);
			
			throw $e;
		}
		
		array_pop($this->buildStack);
		
		return $reflector->newInstanceArgs($instances);
	}
	
	/**
	 * Resolve all of the dependencies from the ReflectionParameters.
	 *
	 * @param \ReflectionParameter[] $dependencies
	 * @return array
	 *
	 */
	protected function resolveDependencies(array $dependencies)
	{
		$results = [];
		
		foreach ($dependencies as $dependency) {
			// If the dependency has an override for this particular build we will use
			// that instead as the value. Otherwise, we will continue with this run
			// of resolutions and let reflection attempt to determine the result.
			if ($this->hasParameterOverride($dependency)) {
				$results[] = $this->getParameterOverride($dependency);
				
				continue;
			}
			
			// If the class is null, it means the dependency is a string or some other
			// primitive type which we can not resolve since it is not a class and
			// we will just bomb out with an error since we have no-where to go.
			$result = is_null(Util::getParameterClassName($dependency)) ? $this->resolvePrimitive($dependency) : $this->resolveClass($dependency);
			
			if ($dependency->isVariadic()) {
				$results = array_merge($results, $result);
			} else {
				$results[] = $result;
			}
		}
		
		return $results;
	}
	
	/**
	 * Determine if the given dependency has a parameter override.
	 *
	 * @param \ReflectionParameter $dependency
	 * @return bool
	 */
	protected function hasParameterOverride(ReflectionParameter $dependency): bool
    {
		return array_key_exists($dependency->name, $this->getLastParameterOverride());
	}
	
	/**
	 * Get a parameter override for a dependency.
	 *
	 * @param \ReflectionParameter $dependency
	 * @return mixed
	 */
	protected function getParameterOverride(ReflectionParameter $dependency): mixed
    {
		return $this->getLastParameterOverride()[$dependency->name];
	}
	
	/**
	 * Get the last parameter override.
	 *
	 * @return array
	 */
	protected function getLastParameterOverride(): array
    {
		return count($this->with) ? end($this->with) : [];
	}
	
	/**
	 * Resolve a non-class hinted primitive dependency.
	 *
	 * @param \ReflectionParameter $parameter
	 * @return mixed
	 *
	 */
	protected function resolvePrimitive(ReflectionParameter $parameter): mixed
    {
		if (!is_null($concrete = $this->getContextualConcrete('$' . $parameter->getName()))) {
			return $concrete instanceof Closure ? $concrete($this) : $concrete;
		}
		
		if ($parameter->isDefaultValueAvailable()) {
			return $parameter->getDefaultValue();
		}
		
		$this->unresolvablePrimitive($parameter);
        return null;
	}
	
	/**
	 * Resolve a class based dependency from the container.
	 *
	 * @param \ReflectionParameter $parameter
	 * @return mixed
	 *
	 */
	protected function resolveClass(ReflectionParameter $parameter): mixed
    {
		try {
			return $parameter->isVariadic() ? $this->resolveVariadicClass($parameter) : $this->make(Util::getParameterClassName($parameter));
		}
			//fixme


			// If we can not resolve the class instance, we will check to see if the value
			// is optional, and if it is we will return the optional parameter value as
			// the value of the dependency, similarly to how we do this with scalars.
		catch (BindingResolutionException $e) {
			if ($parameter->isDefaultValueAvailable()) {
				return $parameter->getDefaultValue();
			}
			
			if ($parameter->isVariadic()) {
				return [];
			}
			
			throw $e;
		}
	}
	
	/**
	 * Resolve a class based variadic dependency from the container.
	 *
	 * @param \ReflectionParameter $parameter
	 * @return mixed
	 */
	protected function resolveVariadicClass(ReflectionParameter $parameter): mixed
    {
		$class_name = Util::getParameterClassName($parameter);
		
		$abstract = $this->getAlias($class_name);
		
		if (!is_array($concrete = $this->getContextualConcrete($abstract))) {
			return $this->make($class_name);
		}
		
		return array_map(function ($abstract) {
			return $this->resolve($abstract);
		}, (array)$concrete);
	}
	
	/**
	 * Throw an exception that the concrete is not instantiable.
	 *
	 * @param string $concrete
	 * @return void
	 *
	 */
	protected function notInstantiable(string $concrete): void
	{
		if (!empty($this->buildStack)) {
			$previous = implode(', ', $this->buildStack);
			
			$message = "Target [$concrete] is not instantiable while building [$previous].";
		} else {
			$message = "Target [$concrete] is not instantiable.";
		}
		throw SuraException::error('err');
//        throw new BindingResolutionException($message);
	}
	
	/**
	 * Throw an exception for an unresolvable primitive.
	 *
	 * @param \ReflectionParameter $parameter
	 * @return void
	 *
	 */
	#[Pure] protected function unresolvablePrimitive(ReflectionParameter $parameter)
	{
		$message = "Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}";
		//FIXME

//        throw SuraException::Error($message);
//        throw new BindingResolutionException($message);
	}
	
	/**
	 * Register a new before resolving callback for all types.
	 *
	 * @param string|\Closure $abstract
	 * @param \Closure|null $callback
	 * @return void
	 */
	public function beforeResolving(string|Closure $abstract, Closure $callback = null)
	{
		if (is_string($abstract)) {
			$abstract = $this->getAlias($abstract);
		}
		
		if ($abstract instanceof Closure && is_null($callback)) {
			$this->globalBeforeResolvingCallbacks[] = $abstract;
		} else {
			$this->beforeResolvingCallbacks[$abstract][] = $callback;
		}
	}
	
	/**
	 * Register a new resolving callback.
	 *
	 * @param string|\Closure $abstract
	 * @param \Closure|null $callback
	 * @return void
	 */
	public function resolving(string|Closure $abstract, Closure $callback = null)
	{
		if (is_string($abstract)) {
			$abstract = $this->getAlias($abstract);
		}
		
		if (is_null($callback) && $abstract instanceof Closure) {
			$this->globalResolvingCallbacks[] = $abstract;
		} else {
			$this->resolvingCallbacks[$abstract][] = $callback;
		}
	}
	
	/**
	 * Register a new after resolving callback for all types.
	 *
	 * @param string|\Closure $abstract
	 * @param \Closure|null $callback
	 * @return void
	 */
	public function afterResolving(string|Closure $abstract, Closure $callback = null)
	{
		if (is_string($abstract)) {
			$abstract = $this->getAlias($abstract);
		}
		
		if ($abstract instanceof Closure && is_null($callback)) {
			$this->globalAfterResolvingCallbacks[] = $abstract;
		} else {
			$this->afterResolvingCallbacks[$abstract][] = $callback;
		}
	}
	
	/**
	 * Fire all of the before resolving callbacks.
	 *
	 * @param string $abstract
	 * @param array $parameters
	 * @return void
	 */
	protected function fireBeforeResolvingCallbacks(string $abstract, array $parameters = [])
	{
		$this->fireBeforeCallbackArray($abstract, $parameters, $this->globalBeforeResolvingCallbacks);
		
		foreach ($this->beforeResolvingCallbacks as $type => $callbacks) {
			if ($type === $abstract || is_subclass_of($abstract, $type)) {
				$this->fireBeforeCallbackArray($abstract, $parameters, $callbacks);
			}
		}
	}
	
	/**
	 * Fire an array of callbacks with an object.
	 *
	 * @param string $abstract
	 * @param array $parameters
	 * @param array $callbacks
	 * @return void
	 */
	protected function fireBeforeCallbackArray(string $abstract, array $parameters, array $callbacks)
	{
		foreach ($callbacks as $callback) {
			$callback($abstract, $parameters, $this);
		}
	}
	
	/**
	 * Fire all of the resolving callbacks.
	 *
	 * @param string $abstract
	 * @param mixed $object
	 * @return void
	 */
	protected function fireResolvingCallbacks(string $abstract, mixed $object)
	{
		$this->fireCallbackArray($object, $this->globalResolvingCallbacks);
		
		$this->fireCallbackArray($object, $this->getCallbacksForType($abstract, $object, $this->resolvingCallbacks));
		
		$this->fireAfterResolvingCallbacks($abstract, $object);
	}
	
	/**
	 * Fire all of the after resolving callbacks.
	 *
	 * @param string $abstract
	 * @param mixed $object
	 * @return void
	 */
	protected function fireAfterResolvingCallbacks(string $abstract, mixed $object)
	{
		$this->fireCallbackArray($object, $this->globalAfterResolvingCallbacks);
		
		$this->fireCallbackArray($object, $this->getCallbacksForType($abstract, $object, $this->afterResolvingCallbacks));
	}
	
	/**
	 * Get all callbacks for a given type.
	 *
	 * @param string $abstract
	 * @param object $object
	 * @param array $callbacksPerType
	 *
	 * @return array
	 */
	protected function getCallbacksForType(string $abstract, object $object, array $callbacksPerType): array
    {
		$results = [];
		
		foreach ($callbacksPerType as $type_ => $callbacks) {
			if ($type_ === $abstract || $object instanceof $type_) {
				$results = array_merge($results, $callbacks);
			}
		}
		
		return $results;
	}
	
	/**
	 * Fire an array of callbacks with an object.
	 *
	 * @param mixed $object
	 * @param array $callbacks
	 * @return void
	 */
	protected function fireCallbackArray(mixed $object, array $callbacks)
	{
		foreach ($callbacks as $callback) {
			$callback($object, $this);
		}
	}
	
	/**
	 * Get the container's bindings.
	 *
	 * @return array
	 */
	public function getBindings(): array
    {
		return $this->bindings;
	}
	
	/**
	 * Get the alias for an abstract if available.
	 *
	 * @param string $abstract
	 * @return string
	 */
	public function getAlias(string $abstract): string
    {
		return isset($this->aliases[$abstract]) ? $this->getAlias($this->aliases[$abstract]) : $abstract;
	}
	
	/**
	 * Get the extender callbacks for a given type.
	 *
	 * @param string $abstract
	 * @return array
	 */
	protected function getExtenders(string $abstract): array
    {
		return $this->extenders[$this->getAlias($abstract)] ?? [];
	}
	
	/**
	 * Remove all of the extender callbacks for a given type.
	 *
	 * @param string $abstract
	 * @return void
	 */
	public function forgetExtenders(string $abstract): void
	{
		unset($this->extenders[$this->getAlias($abstract)]);
	}
	
	/**
	 * Drop all of the stale instances and aliases.
	 *
	 * @param string $abstract
	 * @return void
	 */
	protected function dropStaleInstances(string $abstract): void
	{
		unset($this->instances[$abstract], $this->aliases[$abstract]);
	}
	
	/**
	 * Remove a resolved instance from the instance cache.
	 *
	 * @param string $abstract
	 * @return void
	 */
	public function forgetInstance(string $abstract): void
	{
		unset($this->instances[$abstract]);
	}
	
	/**
	 * Clear all of the instances from the container.
	 *
	 * @return void
	 */
	public function forgetInstances(): void
	{
		$this->instances = [];
	}
	
	/**
	 * Flush the container of all bindings and resolved instances.
	 *
	 * @return void
	 */
	public function flush(): void
	{
		$this->aliases = [];
		$this->resolved = [];
		$this->bindings = [];
		$this->instances = [];
		$this->abstractAliases = [];
	}
	
	/**
	 * Get the globally available instance of the container.
	 *
	 * @return static
	 */
	public static function getInstance(): static
	{
		if (is_null(static::$instance)) {
			static::$instance = new static();
		}
		
		return static::$instance;
	}
	
	/**
	 * Set the shared instance of the container.
	 *
	 * @param \Contracts\Container\Container|null $container
	 * @return \Contracts\Container\Container|static
	 */
//    public static function setInstance(ContainerContract $container = null)
	public static function setInstance($container = null)
	{
		return static::$instance = $container;
	}

    /**
     * Determine if a given offset exists.
     *
     * @param $offset
     * @return bool
     */
	public function offsetExists($offset): bool
    {
		return $this->bound($offset);
	}
	
	/**
	 * Get the value at a given offset.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->make($key);
	}
	
	/**
	 * Set the value at a given offset.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		$this->bind($key, $value instanceof Closure ? $value : function () use ($value) {
			return $value;
		});
	}
	
	/**
	 * Unset the value at a given offset.
	 *
	 * @param string $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->bindings[$offset], $this->instances[$offset], $this->resolved[$offset]);
	}
	
	/**
	 * Dynamically access container services.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get(string $key): mixed
	{
		return $this[$key];
	}
	
	/**
	 * Dynamically set container services.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function __set(string $key, mixed $value)
	{
		$this[$key] = $value;
	}
}