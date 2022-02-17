<?php

namespace App\GraphQL\Directives;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Exceptions\AuthorizationException;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SelfOrCanDirective extends BaseDirective implements FieldMiddleware
{
    // TODO implement the directive https://lighthouse-php.com/master/custom-directives/getting-started.html

    public static function definition(): string
    {
        return
            /** @lang GraphQL */
            <<<'GRAPHQL'
Check if user is trying to access their own account and, if not, check if they have required permission.
"""
directive @selfOrCan(
  """
  The ability to check permissions for.
  """
  ability: String!
) repeatable on FIELD_DEFINITION
GRAPHQL;
    }

    /**
     * Wrap around the final field resolver.
     *
     * @param  \Nuwave\Lighthouse\Schema\Values\FieldValue  $value
     * @param  \Closure  $next
     * @return \Nuwave\Lighthouse\Schema\Values\FieldValue
     */
    public function handleField(FieldValue $value, Closure $next)
    {
        $resolver = $value->getResolver();
        $ability = $this->directiveArgValue('ability');

        return $next(
            $value->setResolver(
                function ($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo) use ($ability, $resolver) {
                    $requestedUserId = (int) $args['id'];
                    $authUserId = $context->user()->id;
                    $authUser = Auth::user();
                    if (($requestedUserId !== $authUserId) && !$authUser->hasPermissionTo($ability)) {
                        throw new AuthorizationException("You are not authorized to access {$this->nodeName()}");
                    }

                    return call_user_func_array($resolver, func_get_args());
                }
            )
        );
    }
}
