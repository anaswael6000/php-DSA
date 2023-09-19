<?php

require_once "vendor/autoload.php";
require_once "app/Data_structures/graph.php";

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProviderExternal;

final class graphTest extends TestCase
{
    public $adjacencyMatrix;
    public $adjacencyList;

    public function setUp():void
    {
        $this->adjacencyMatrix = new app\Data_structures\adjacencyMatrix;
        $this->adjacencyList = new app\Data_structures\adjacencyList;
    }

    // Adjacency matrix tests
    
    #[DataProviderExternal('graphTestDataProviders', 'test_building_a_graph_data_provider')]
    public function test_building_an_adjacencyMatrix($vertices)
    {
        $this->adjacencyMatrix->build($vertices);
        // the second parameter only counts the number of elements in the second dimension of the array
        $this->assertEquals(count($vertices) ** 2 ,sizeof($this->adjacencyMatrix->data, 1) - count($this->adjacencyMatrix->data));
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_adding_new_vertices_to_the_graph_data_provider')]
    public function test_adding_new_vertices_to_the_adjacencyMatrix($vertices, $newVertices)
    {
        $this->adjacencyMatrix->build($vertices);
        $this->adjacencyMatrix->addVertices($newVertices);
        $this->assertEquals((count($vertices) + count($newVertices)) ** 2, sizeof($this->adjacencyMatrix->data, 1) - count($this->adjacencyMatrix->data));
    }
    
    #[DataProviderExternal('graphTestDataProviders', 'test_removing_a_vertex_data_provider')]
    public function test_removing_a_vertex_from_the_adjacencyMatrix($vertices, $vertex, $sum_of_new_elements)
    {
        $this->adjacencyMatrix->build($vertices);
        $this->adjacencyMatrix->removeVertex($vertex);
        $this->assertEquals($sum_of_new_elements ,sizeof($this->adjacencyMatrix->data, 1));
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_setting_edges_between_the_vertices_data_provider')]
    public function test_setting_edges_between_the_vertices_in_the_adjacencyMatrix($vertices, $edges)
    {
        $this->adjacencyMatrix->build($vertices);
        $this->adjacencyMatrix->setEdges($edges);
        foreach($edges as $edge)
        {
            $this->assertEquals(1, $this->adjacencyMatrix->data[$edge[0]][$edge[1]]);
            $this->assertEquals(1, $this->adjacencyMatrix->data[$edge[1]][$edge[0]]);
        }
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_setting_edges_between_the_vertices_data_provider')]
    public function test_checking_if_certain_edges_exist_in_the_adjacencyMatrix($vertices, $edges)
    {
        $this->adjacencyMatrix->build($vertices);
        $this->adjacencyMatrix->setEdges($edges);
        $this->assertTrue($this->adjacencyMatrix->edgesExist($edges));
        $otherEdges = [[200, 15], [300, 140]];
        $this->assertFalse($this->adjacencyMatrix->edgesExist($otherEdges));
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_removing_an_edge_data_provider')]
    public function test_removing_an_edge_from_the_adjacencyMatrix($vertices, $edges)
    {
        $this->adjacencyMatrix->build($vertices);
        $this->adjacencyMatrix->setEdges($edges);
        $this->adjacencyMatrix->removeEdges($edges);

        foreach($edges as $edge)
        {
            $this->assertEquals(0, $this->adjacencyMatrix->data[$edge[0]][$edge[1]]);
            $this->assertEquals(0, $this->adjacencyMatrix->data[$edge[1]][$edge[0]]);
            $this->assertFalse($this->adjacencyMatrix->edgesExist($edges));
        }
    }

    // Now let's dive into the Adjacency List tests

    #[DataProviderExternal('graphTestDataProviders', 'test_building_a_graph_data_provider')]
    public function test_building_an_adjacencyList($vertices)
    {
        $this->assertEmpty($this->adjacencyList->data);
        $this->adjacencyList->addVertices($vertices);
        $this->assertEquals(count($vertices), count($this->adjacencyList->data));
        foreach($vertices as $value)
        {
            $this->assertTrue(isset($this->adjacencyList->data[$value]));
            // We use a dynamic array instead of a linked List in this implementation for the sake of simplicity
            $this->assertIsArray($this->adjacencyList->data[$value]);
        }
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_adding_new_vertices_to_the_graph_data_provider')]
    public function test_adding_new_vertices_to_the_adjacencyList($vertices, $newVertices)
    {
        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->addVertices($newVertices);
        $this->assertEquals(count($vertices) + count($newVertices), count($this->adjacencyList->data));
    }
    
    #[DataProviderExternal('graphTestDataProviders', 'test_removing_a_vertex_data_provider')]
    public function test_removing_a_vertex_from_the_adjacencyList($vertices, $vertex, $sum_of_new_elements)
    {
        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->removeVertex($vertex);
        $this->assertTrue(!isset($this->adjacencyList->data[$vertex]));
        $this->assertEquals(count($vertices) - 1, count($this->adjacencyList->data));
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_setting_edges_between_the_vertices_data_provider')]
    public function test_setting_edges_between_the_vertices_in_the_adjacencyList($vertices, $edges)
    {
        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->setEdges($edges);

        foreach($edges as $edge)
        {
            $this->assertTrue(in_array($edge[1], $this->adjacencyList->data[$edge[0]]));
            $this->assertTrue(in_array($edge[0], $this->adjacencyList->data[$edge[1]]));
        }
        // clear the adjacency list
        $this->adjacencyList->data = [];

        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->setEdges($edges, true);
        foreach($edges as $edge)
        {
            $this->assertTrue(in_array($edge[1], $this->adjacencyList->data[$edge[0]]));
            $this->assertFalse(in_array($edge[0], $this->adjacencyList->data[$edge[1]]));
        }
    }
    
    public function test_setting_weighted_edges()
    {   
        $this->adjacencyList->addVertices([1, 2, 3, 4]);
        $edges = [[1, 2, 5], [3, 1, 11], [4, 2, 7], [2, 3, 15]];
        $this->adjacencyList->setWeightedEdges($edges, true);

        foreach($edges as [$source, $destination, $weight])
        {
            // Check that the source vertex points to the destination and the weight is present
            $this->assertTrue(in_array([$weight, $destination], $this->adjacencyList->data[$source]));

            // Check that the last edge of the source vertex is the newly set one 
            $last_index = count($this->adjacencyList->data[$source]) - 1;
            $this->assertEquals([$weight, $destination], $this->adjacencyList->data[$source][$last_index]);
        }
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_setting_edges_between_the_vertices_data_provider')]
    public function test_checking_if_certain_edges_exist_in_the_adjacencyList($vertices, $edges)
    {
        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->setEdges($edges);
        $this->assertTrue($this->adjacencyList->edgesExist($edges));
        $nonExistingEdges = [[200, 15], [300, 140]];
        $this->assertFalse($this->adjacencyList->edgesExist($nonExistingEdges));
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_removing_an_edge_data_provider')]
    public function test_clearing_the_adjacencyList_edges($vertices, $edges)
    {
        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->setEdges($edges);
        $this->adjacencyList->clearEdges();
        $this->assertFalse($this->adjacencyList->edgesExist($edges));
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_setting_edges_between_the_vertices_data_provider')]
    public function test_transforming_the_graph_to_the_transpose($vertices, $edges)
    {
        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->setEdges($edges, true);
        $reversed_adjacencyList = $this->adjacencyList->transpose();
        foreach($this->adjacencyList->edges as [$source, $destination])
        {
            $this->assertTrue(in_array($source, $reversed_adjacencyList[$destination]));
            $this->assertFalse(in_array($destination, $reversed_adjacencyList[$source]));
        }
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_breadth_first_search_data_provider')]
    public function test_breadth_first_search($vertices, $edges, $vertex, $expected_output)
    {
        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->setEdges($edges);
        $this->expectOutputString($expected_output);
        $this->adjacencyList->BFS($vertex);
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_depth_first_search_data_provider')]
    public function test_depth_first_search($vertices, $edges, $vertex, $expected_output)
    {
        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->setEdges($edges);
        $this->assertEquals($expected_output, $this->adjacencyList->DFS($vertex));
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_detecting_a_cycle_in_a_graph_data_provider')]
    public function test_detecting_a_cycle_in_a_graph($vertices, $edges_forming_a_cycle, $directed, $edges_not_forming_a_cycle)
    {
        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->setEdges($edges_forming_a_cycle, $directed);
        $this->assertTrue($this->adjacencyList->cyclesExist($this->adjacencyList->data));
        $this->adjacencyList->clearEdges();
        $this->adjacencyList->setEdges($edges_not_forming_a_cycle, $directed);
        $this->assertFalse($this->adjacencyList->cyclesExist($this->adjacencyList->data));
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_dijkstra_algorithm_data_provider')]
    public function test_dijkstra_algorithm($vertices, $edges, $directed, $starting_vertex, $expected_shortest_distances)
    {
        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->setWeightedEdges($edges, $directed);
        // The used method compares the first dimension of the array by values only and the second dimension of the array by values and order
        $this->assertEquals($expected_shortest_distances, $this->adjacencyList->dijkstra($starting_vertex));
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_bellman_ford_algorithm_data_provider')]
    public function test_bellman_ford_algorithm($vertices, $edges, $directed, $starting_vertex, $expected_shortest_distances)
    {
        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->setWeightedEdges($edges, $directed);
        // The used method compares the first dimension of the array by values only and the second dimension of the array by values and order
        $this->assertEquals($expected_shortest_distances, $this->adjacencyList->bellman_ford($starting_vertex));
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_kruskal_algorithm_data_provider')]
    public function test_kruskal_algorithm($vertices, $edges, $expected_MST)
    {
        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->setWeightedEdges($edges, false);
        ksort($expected_MST);
        $actual_MST = $this->adjacencyList->kruskal();
        ksort($actual_MST);
        $this->assertEquals($expected_MST, $actual_MST);
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_prim_algorithm_data_provider')]
    public function test_prim_algorithm($vertices, $edges, $expected_MST)
    {
        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->setWeightedEdges($edges, false);
        ksort($expected_MST);
        $actual_MST = $this->adjacencyList->prim();
        ksort($actual_MST);
        $this->assertEquals($expected_MST, $actual_MST);
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_topological_sorting_data_provider')]
    public function test_topological_sorting($vertices, $edges, $expected_output_using_DFS, $expected_output_from_kahn)
    {
        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->setEdges($edges, true);
        $this->assertEquals($expected_output_using_DFS, $this->adjacencyList->topological_sort());
        $this->assertEquals($expected_output_from_kahn, $this->adjacencyList->kahn());
    }

    public function test_finding_articulation_points()
    {
        $this->adjacencyList->addVertices([1, 2, 3, 4, 5]);
        $this->adjacencyList->setEdges([[1, 2], [1, 3], [2, 3], [3, 4], [4, 5]]);
        $this->assertEquals([3, 4], $this->adjacencyList->find_articulation_points());
    }
    
    public function test_finding_bridges()
    {
        $this->adjacencyList->addVertices([0, 1, 2, 3]);
        $this->adjacencyList->setEdges([[0, 1], [1, 2], [2, 3]]);
        $this->assertEquals([[0, 1], [1, 2], [2, 3]], $this->adjacencyList->find_bridges());
        $this->adjacencyList->clearEdges();
        $this->adjacencyList->addVertex(4);
        $this->adjacencyList->setEdges([[0, 1], [0, 2], [0, 3], [3, 4], [1, 2]]);
        $this->assertEquals([[0, 3], [3, 4]], $this->adjacencyList->find_bridges());
    }

    public function test_finding_connected_components()
    {
        $this->adjacencyList->addVertices([0, 1, 2, 3, 4]);
        $this->adjacencyList->setEdges([[0, 1], [1, 2], [3, 4]]);
        $this->assertEquals([[0, 1, 2], [3, 4]], $this->adjacencyList->find_connected_components());
    }

    #[DataProviderExternal('graphTestDataProviders', 'test_finding_SCCs_data_provider')]
    public function test_finding_SCCs($vertices, $edges, $expected_output_from_kosaraju, $expected_output_from_tarjan)
    {
        $this->adjacencyList->addVertices($vertices);
        $this->adjacencyList->setEdges($edges, true);
        $this->assertEquals($expected_output_from_kosaraju, $this->adjacencyList->find_SCCs_kosaraju());
        $this->assertEquals($expected_output_from_tarjan, $this->adjacencyList->find_SCCs_tarjan());
    }

    public function test_checking_whether_the_graph_is_bi_connected_or_not()
    {
        $this->adjacencyList->addVertices([1, 2, 3]);
        $this->adjacencyList->setEdges([[1, 3], [2, 3]]);
        $this->assertFalse($this->adjacencyList->bi_connected());
        $this->adjacencyList->setEdges([[2, 1]]);
        $this->assertTrue($this->adjacencyList->bi_connected());
        $this->adjacencyList->data = $this->adjacencyList->vertices = $this->adjacencyList->edges = [];
        $this->adjacencyList->addVertices([1, 2]);
        $this->adjacencyList->setEdges([[1, 2]]);
        $this->assertTrue($this->adjacencyList->bi_connected());
    }
}

class graphTestDataProviders
{
    public static function test_building_a_graph_data_provider()
    {
        return [
            // Order of input   1:graph values
            [[1, 2, 3, 4]],
            [[18, 20, 5]],
            [[1]],
        ];
    }

    public static function test_adding_new_vertices_to_the_graph_data_provider()
    {
        return [
            // Order of input  1: initial graph values  2: New vertices
            [[1, 2, 3, 4], [5]],
            [[18, 20, 5], [1, 2]],
            [[1], [2, 3]],
        ];
    }
    
    public static function test_removing_a_vertex_data_provider()
    {
        return [
            // Order of input  1:Graph input  2:A vertex to remove  3:The sum of elements of the new graph

            // Target a first row first column vertex removal
            [[1, 2, 3], 1, 6],
            
            // Target a last row last column vertex removal
            [[1, 2, 3], 3, 6],

            // Target a half row half column vertex removal
            [[0, 1, 2, 3, 4], 2, 20],
        ];
    }

    public static function test_setting_edges_between_the_vertices_data_provider()
    {
        return [
            // order of input 1:graph input  2:An array of Edges
            [[1, 2, 3, 4], [[1, 4], [2, 3], [1, 2]] ],
            [[18, 20, 5], [[20, 5]]],
        ];
    }

    public static function test_removing_an_edge_data_provider()
    {
        return [
            [[1, 2, 3, 4], [[1, 3], [2, 4], [1, 4]]],
            [["anas", "sarah", "mo3z"], [["anas", "sarah"], ["sarah", "mo3z"]]],
        ];
    }
    
    public static function test_breadth_first_search_data_provider()
    {
        return [
            // Order of input  1:Graph input  2:Edges  3:Vertex  4:Expected Breadth first search output
            [["anas", "sarah", "omar", "roger"], [["anas", "omar"], ["sarah", "omar"], ["anas", "roger"], ["omar", "roger"], ["roger", "omar"]], "anas", 
            "anas omar roger sarah "],

            [[1, 2, 3, 4], [[1, 4], [2, 3], [3, 4], [1, 3], [4, 2]], 1, "1 4 3 2 "],
        ];
    }
    
    public static function test_depth_first_search_data_provider()
    {
        return [
            // Order of input  1:Graph input  2:Edges  3:Vertex  4:Expected Breadth first search output
            [["anas", "sarah", "omar", "roger"], [["anas", "omar"], ["sarah", "omar"], ["anas", "roger"], ["omar", "roger"]], "roger", 
            ["roger", "omar", "sarah", "anas"]],
    
            [[1, 2, 3, 4], [[1, 4], [2, 3], [3, 4], [1, 3], [4, 2]], 3, [3, 1, 4, 2]],

            [[1, 2, 3, 4], [[1, 4], [2, 3], [3, 4], [1, 3], [4, 2]], 2, [2, 4, 1, 3]],
        ];
    }

    public static function test_detecting_a_cycle_in_a_graph_data_provider()
    {
        return [
            // Order of input  1:Values to build the graph  2:Edges forming a cycle  3:Whether the edges are weighted or not
            // 4:Whether the edges are directed or not 3:Edges not forming a cycle

            // Edges forming one cycle
            [[1, 2, 3, 4], [[1, 2], [3, 1], [2, 3]], true, [[1, 2], [1, 3], [3, 2]]],

            // Edges forming one cycle
            [[1, 2, 3, 4, 5], [[1, 2], [1, 3], [2, 5], [3, 4], [4, 1]], true, [[1, 2], [4, 3], [5, 2], [1, 3], [3, 2]]],
            
            // Edges forming multiple cycles
            [[1, 2, 3, 4], [[1, 2], [3, 1], [2, 3], [2, 4], [4, 3]], true, [[1, 2], [1, 3], [3, 2]]],
        ];
    }

    public static function test_dijkstra_algorithm_data_provider()
    {
        return [
            // Order of input  1:Graph input  2:Edges  3:Whether the edges is directed or not  4:Starting vertex  5:Expected shortest distances
            [
            // Graph input
            [0, 1, 2, 3, 4, 5, 6, 7, 8], 
            // Edges
            [[0, 1, 4], [1, 2, 8], [2, 3, 7], [3, 4, 9], [4, 5, 10], [5, 6, 2], [6, 7, 1], [7, 8, 7], [2, 8, 2], [2, 5, 4], [3, 5, 14], [6, 8, 6],
            [1, 7, 11], [0, 7, 8]],
            // directed or not 
            false,
            // Starting vertex
            0, 
            // Expected shortest distances
            [0 => 0, 1 => 4, 2 => 12, 3 => 19, 4 => 21, 5 => 11, 6 => 9, 7 => 8, 8 => 14]],
             
            [["A", "B", "C", "D", "E"], [["A", "B", 4], ["A", "C", 2], ["B", "C", 3], ["B", "D", 2], ["B", "E", 3],
             ["C", "D", 4], ["C", "B", 1], ["C", "E", 5], ["E", "D", 1]], true, "A", ["A" => 0, "C" => 2, "B" => 3, "D" => 5, "E" => 6]],
            ];
    }

    public static function test_bellman_ford_algorithm_data_provider()
    {
        return [
            // Order of input  1:Graph input  2:Edges  3:Whether the edges is directed or not  4:Starting vertex  5:Expected shortest distances

            // The same tests as dijkstra
            [[0, 1, 2, 3, 4, 5, 6, 7, 8], 
            [[0, 1, 4], [1, 2, 8], [2, 3, 7], [3, 4, 9], [4, 5, 10], [5, 6, 2], [6, 7, 1], [7, 8, 7], [2, 8, 2], [2, 5, 4], [3, 5, 14], [6, 8, 6],
            [1, 7, 11], [0, 7, 8]], false, 0, [0 => 0, 1 => 4, 2 => 12, 3 => 19, 4 => 21, 5 => 11, 6 => 9, 7 => 8, 8 => 14]],
             
            [["A", "B", "C", "D", "E"], [["A", "B", 4], ["A", "C", 2], ["B", "C", 3], ["B", "D", 2], ["B", "E", 3],
             ["C", "D", 4], ["C", "B", 1], ["C", "E", 5], ["E", "D", 1]], true, "A", ["A" => 0, "C" => 2, "B" => 3, "D" => 5, "E" => 6]],

            // Additional test for graphs with negative weight edges
            [["S", "E", "D", "C", "B", "A"], [["S", "E", 8], ["S", "A", 10], ["E", "D", 1], ["A", "C", 2],
             ["D", "C", -1], ["D", "A", -4], ["C", "B", -2], ["B", "A", 1]] , true, "S", ["S" => 0, "A" => 5, "B" => 5, "C" => 7, "D" => 9, "E" => 8]],  
        ];
    }

    public static function test_kruskal_algorithm_data_provider()
    {
        return [
            // Order of input  1:Graph input  2:Edges  3:Expected MST
            [[0, 1, 2, 3, 4, 5, 6, 7, 8], 
            [[0, 1, 4], [2, 3, 7], [3, 4, 9], [4, 5, 10], [5, 6, 2], [6, 7, 1], [7, 8, 7], 
            [2, 8, 2], [2, 5, 4], [3, 5, 14], [6, 8, 6], [1, 7, 11], [0, 7, 8]], [0 => [[4, 1], [8, 7]], 1 => [[4, 0]], 7 => [[1, 6], [8, 0]], 
            6 => [[1, 7], [2, 5]], 5 => [[2, 6], [4, 2]], 2 => [[2, 8], [4, 5], [7, 3]], 8 => [[2, 2]], 3 => [[7, 2], [9, 4]], 4 => [[9, 3]]]
            ],

            [["A", "B", "C", "D", "E"],
            [["A", "B", 1], ["A", "C", 7], ["A", "D", 10], ["A", "E", 5], ["B", "C", 3], ["C", "D", 4], ["D", "E", 2]], 
            ["A" => [[1, "B"]], "B" => [[1, "A"], [3, "C"]], "C" => [[3, "B"], [4, "D"]], "D" => [[2, "E"], [4, "C"]], "E" => [[2, "D"]]]
            ],
        ];
    }
    
    public static function test_prim_algorithm_data_provider()
    {
        return [
            // Order of input  1:Graph input  2:Edges  3:Expected MST
            [[0, 1, 2, 3, 4, 5, 6, 7, 8], 
            [[0, 1, 4], [2, 3, 7], [3, 4, 9], [4, 5, 10], [5, 6, 2], [6, 7, 1], [7, 8, 7], 
            [2, 8, 2], [2, 5, 4], [3, 5, 14], [6, 8, 6], [1, 7, 11], [0, 7, 8]], [0 => [[4, 1], [8, 7]], 1 => [[4, 0]], 2 => [[4, 5], [2, 8], [7, 3]]
            , 8 => [[2, 2]], 3 => [[7, 2], [9, 4]], 4 => [[9, 3]], 5 => [[2, 6], [4, 2]], 6 => [[1, 7], [2, 5]], 7 => [[8, 0], [1, 6]]]
            ],

            [["A", "B", "C", "D", "E"],
            [["A", "B", 1], ["A", "C", 7], ["A", "D", 10], ["A", "E", 5], ["B", "C", 3], ["C", "D", 4], ["D", "E", 2]], 
            ["A" => [[1, "B"]], "B" => [[1, "A"], [3, "C"]], "C" => [[3, "B"], [4, "D"]], "D" => [[4, "C"], [2, "E"]], "E" => [[2, "D"]]]
            ],
        ];
    }

    public static function test_topological_sorting_data_provider()
    {
        return [
            // Input:  1:Graph input   2:Edges   3:Expected Output from dfs-based implementation   4:Expected Output from kahn
            // Note that any graph can have more than one topological sorting
            [[0, 1, 2, 3, 4, 5], [[5, 0], [5, 2], [4, 0], [4, 1], [2, 3], [3, 1]], [5, 4, 2, 3, 1, 0], [4, 5, 0, 2, 3, 1]],
        ];
    }

    public static function test_finding_SCCs_data_provider()
    {
        return [
            // Order of input:  1:Vertices   2:Edges   3:Expected output from kosaraju   4:Expected output from tarjan  
            // Note that like the topological sorting data provider the only difference between the third and the fourth input is the order 
            [[1, 2, 3, 4, 5, 6, 7], [[1, 2], [2, 3], [3, 4], [3, 6], [4, 1], [4, 5], [5, 6], [6, 7], [7, 5]],
             [[1, 4, 3, 2], [5, 7, 6]], [[7, 6, 5], [4, 3, 2, 1]]],
            [["a", "b", "c", "d", "e"], [["a", "c"], ["c", "b"], ["b", "a"], ["c", "d"], ["d", "e"]], 
             [["a", "b", "c"], ["d"], ["e"]], [["e"], ["d"], ["b", "c", "a"]]]
        ];
    }
}