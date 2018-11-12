<?php

class WidgetQueriesTest extends \Codeception\TestCase\WPTestCase
{

    public function setUp()
    {
        // before
        parent::setUp();

        // your set up methods here
    }

    public function tearDown()
    {
        // your tear down methods here

        // then
        parent::tearDown();
    }

    // tests
    public function testWidgetQuery()
    {
        /**
         * Retrieve default Meta widget
         */
        $widget_id = 'meta-2';
        $widget = \WPGraphQL\Data\DataSource::resolve_widget($widget_id);
        
        /**
         * use --debug flag to view
         */
        \Codeception\Util\Debug::debug( $widget );

        $relay_id = \GraphQLRelay\Relay::toGlobalId( 'widget', $widget_id );

		$query = '
		query widgetQuery($id: ID!){
			widget( id: $id ) {
                widgetId
                name
                id
                ... on MetaWidget {
                    title
                }
			}
		}
        ';

        $variables = array( 'id' => $relay_id );
        
        $actual = do_graphql_request( $query, 'widgetQuery', $variables );

        $expected = [
            'data' => [
                'widget' => [
                  'widgetId' => 'meta-2',
                  'name' => 'Meta',
                  'id' => $relay_id,
                  'title' => 'Meta'
                ]
            ]
        ];

        /**
         * use --debug flag to view
         */
        \Codeception\Util\Debug::debug( $relay_id );

        /**
         * use --debug flag to view
         */
        \Codeception\Util\Debug::debug( $actual );

		/**
		 * Compare the actual output vs the expected output
		 */
        $this->assertEquals( $expected, $actual );
    }

}