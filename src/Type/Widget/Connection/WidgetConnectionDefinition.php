<?php

namespace WPGraphQL\Type\Widget\Connection;

use GraphQLRelay\Relay;
use WPGraphQL\Types;
use WPGraphQL\Type\WPInputObjectType;

/**
 * Class WidgetConnectionDefinition
 *
 * @package WPGraphQL\Type\Widget\Connection
 * @since   0.0.31
 */
class WidgetConnectionDefinition {

	/**
	 * Stores the Relay connection for Widget
	 *
	 * @var array $connection
	 * @access private
	 */
	private static $connection;

	/**
	 * Stores the where_args Input Object Type
	 *
	 * @var \WPGraphQL\Type\WPInputObjectType $where_args
	 */
	private static $where_args;

	/**
	 * Stores the fields for the $where_args
	 *
	 * @var array $where_fields
	 */
	private static $where_fields;

	/**
	 * Create the Relay connection for Widgets.
	 *
	 * @param string $from_type Connection type.
	 * @return mixed
	 * @since  0.0.30
	 */
	public static function connection( $from_type = 'Root' ) {

		if ( null === self::$connection ) {

			$connection = Relay::connectionDefinitions( [
				'nodeType' => Types::widget(),
				'name'     => 'Widgets',
				'connectionFields' => function() {
					return [
						'nodes' => [
							'type'        => Types::list_of( Types::widget() ),
							'description' => __( 'The nodes of the connection, without the edges', 'wp-graphql' ),
							'resolve'     => function( $source, $args, $context, $info ) {
								return ! empty( $source['nodes'] ) ? $source['nodes'] : [];
							},
						],
					];
				},
			] );

			$args = [
				'where' => [
					'name' => 'where',
					'type' => self::where_args(),
				],
			];

			self::$connection = [
				'type'        => $connection['connectionType'],
				'description' => __( 'A collection of widget objects', 'wp-graphql' ),
				'args'        => array_merge( Relay::connectionArgs(), $args ),
				'resolve'     => [ __NAMESPACE__ . '\\WidgetConnectionResolver', 'resolve' ],
			];
		}

		return ! empty( self::$connection ) ? self::$connection : null;
	}

	/**
	 * Defines the "where" args that can be used to query menus
	 *
	 * @return WPInputObjectType
	 */
	private static function where_args() {

		if ( null === self::$where_args ) {
			
			self::$where_args = new WPInputObjectType( [
				'name'   => 'WidgetQueryArgs',
				'fields' => function() {
					return self::where_fields();
				},
			] );
		}


		return ! empty( self::$where_args ) ? self::$where_args : null;

	}

	/**
	 * This defines the fields to be used in the $where_args input type
	 *
	 * @return array|mixed
	 */
	private static function where_fields() {
		if ( null === self::$where_fields ) {
			$fields = [

			];

			self::$where_fields = WPInputObjectType::prepare_fields( $fields, 'WidgetQueryArgs' );
		}

		return self::$where_fields;
	}
}