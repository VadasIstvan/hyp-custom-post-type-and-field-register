<?php
/*
Plugin Name: Register Metabox Post Type and Custom Fields to Graphql
Description: RRegister Metabox Post Type and Custom Fields to graphql.
Version: 1.0
Author: WhiteX
*/
//Register WORK custom post type to graphql
add_filter('register_post_type_args', function ($args, $post_type) {

    // Change this to the post type you are adding support for
    if ('work' === $post_type) {
        $args['show_in_graphql'] = true;
        $args['graphql_single_name'] = 'Work';
        $args['graphql_plural_name'] = 'Works';
    }

    return $args;
}, 10, 2);

//add custom field to spots graphql type
add_action('graphql_register_types', function () {
    register_graphql_field('Work', 'extraSpots', [
        'type' => [
            'list_of' => 'String'
        ],
        'description' => __('Additional Spots', 'wp-graphql'),
        'fields' => [
            'title' => [
                'type' => 'String',
            ],
            'content' => [
                'type' => 'String',
            ]
        ],
        'resolve' => function ($post) {
            $spots = rwmb_meta('additional_spots_group', [], $post->ID);
            $spotsArray = [];
            foreach ($spots as $spot) {
                array_push($spotsArray, $spot['video_url']);
            }
            return $spotsArray;
        }
    ]);
});

//Register the type first, with the desired schema structure
add_action('graphql_register_types', 'register_credit_type');

function register_credit_type()
{
    register_graphql_object_type('CreditType', [
        'description' => __("Credits of a Project", 'wp-graphql'),
        'fields' => [
            'title' => [
                'type' => 'String',
                'description' => __('Role in the project', 'wp-graphql'),
            ],
            'name' => [
                'type' => 'String',
                'description' => __('Name of the person', 'wp-graphql'),
            ],
        ],
    ]);
}

//adding the registered field type to the desired content type and resolving the data
add_action('graphql_register_types', 'register_credit_field');

function register_credit_field()
{
    register_graphql_field('Work', 'creditInfo', [
        'type' => [
            'list_of' => 'CreditType'
        ],
        'description' => __('Credits of project', 'wp-graphql'),
        'fields' => [
            'title' => [
                'type' => 'String',
            ],
            'name' => [
                'type' => 'String',
            ]
        ],
        'resolve' => function ($post) {
            $credits = rwmb_meta('credits_group', [], $post->ID);

            foreach ($credits as $i => $credit) {
                $credits[$i] = [
                    'title' => $credit['title'],
                    'name' => $credit['name']
                ];
            }

            return $credits;
        }
    ]);
}

//add custom field to spots graphql type
add_action('graphql_register_types', function () {
    register_graphql_field('Work', 'serviceInfo', [
        'type' => [
            'list_of' => 'String'
        ],
        'description' => __('services list', 'wp-graphql'),
        'fields' => [
            'title' => [
                'type' => 'String',
            ],
            'name' => [
                'type' => 'String',
            ]
        ],
        'resolve' => function ($post) {
            $services = rwmb_meta('services', [], $post->ID);
            $servicesArray = [];
            foreach ($services as $service) {
                array_push($servicesArray, $service->name);
            }
            return $servicesArray;
        }
    ]);
});
