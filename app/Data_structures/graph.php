<?php

namespace app\Data_structures;

include "app/Data_structures/queue.php";

class adjacencyMatrix
{
    public $data = [];

    public function build($values)
    {
        $number_of_values = count($values);
        // Iterate over the rows
        for ($x = 0; $x < $number_of_values; $x++)
        {
            // Iterate over the columns
            for ($y = 0; $y < $number_of_values; $y++)
            {
                $this->data[$values[$x]][$values[$y]] = 0;
            }
        }
    }

    public function addVertices($newValues)
    {
        foreach($newValues as $value)
        {
            $this->addVertex($value);
        }
    }

    public function addVertex($value)
    {
        $keys = array_keys($this->data);
        foreach($keys as $key)
        {
            $this->data[$value][$key] = 0;
        }

        foreach($this->data as &$row)
        {
            $row[$value] = 0;
        }
    }
    
    public function removeVertex($vertex)
    {
       // Remove the row
       unset($this->data[$vertex]);

       // Remove the column
       foreach($this->data as &$row)
       {
            unset($row[$vertex]);
       }

    }

    public function setEdges(array $edges)
    {
        foreach($edges as $edge)
        {
            $this->data[$edge[0]][$edge[1]] = 1;
            $this->data[$edge[1]][$edge[0]] = 1;
        }
    }

    public function edgesExist($edges)
    {
        $keys = array_keys($this->data);
        if(!in_array($edges[0][0], $keys) || !in_array($edges[0][1], $keys))
        {
            return false;
        }
        $check = true;
        foreach($edges as $edge)
        {
            if($this->data[$edge[0]][$edge[1]] !== 1 || $this->data[$edge[1]][$edge[0]] !== 1)
            {
                $check = false;
            }
        }
        return $check;
    }

    public function removeEdges($edges)
    {
        foreach($edges as $edge)
        {
            $this->data[$edge[0]][$edge[1]] = 0;
            $this->data[$edge[1]][$edge[0]] = 0;
        }
    }

}

class adjacencyList
{
    /* Structure: [ $source => [[$weight, $destination], [$weight, $destination]], $another_source => [[$weight, $destination], etc]]  For weighted graphs 
    For UnWeightedGraphs   $source => [$destination, $Another_destination], $another_source => [$destination, $other_destination] */
    public $data;

    // Structure: [$vertex1, $vertex2, etc]
    public $vertices;

    // Structure [[$source1, $destination1, $weight1], [$source2, $destination2, $weight2]]
    public $edges;

    public function addVertices($vertices)
    {
        foreach($vertices as $vertex)
        {
            $this->addVertex($vertex);
        }
    }

    public function addVertex($vertex)
    {
        $this->data[$vertex] = [];
        $this->vertices[] = $vertex;
    }

    public function removeVertex($vertex)
    {
        unset($this->data[$vertex]);
        $index = array_search($vertex, $this->vertices);
        unset($this->vertices[$index]);
    }

    public function setEdges($edges, $directed = false)
    {
        foreach($edges as [$source, $destination])
        {
            $this->data[$source][] = $destination; 
            $this->edges[] = [$source, $destination];
            if ($directed) continue;
            $this->data[$destination][] = $source; 
        }
    }

    public function setWeightedEdges($edges, $directed = false)
    {
        foreach($edges as [$source, $destination, $weight])
        {
            $this->data[$source][] = [$weight, $destination];
            $this->edges[] = [$source, $destination, $weight];
            if ($directed) continue;
            $this->data[$destination][] = [$weight, $source];
            $this->edges[] = [$destination, $source, $weight];
        }
    }

    public function edgesExist($edges, $directed = false)
    {
        foreach($edges as [$source, $destination])
        {
            if (!isset($this->data[$source]) || !in_array($destination, $this->data[$source])) return false;
            if ($directed) continue;
            if (!isset($this->data[$destination]) || !in_array($source, $this->data[$destination])) return false;
        }
        return true;
    }

    public function clearEdges()
    {
        $this->edges = [];
        foreach($this->data as $vertex => $edges)
        {
            $this->data[$vertex] = [];
        }
    }

    public function transpose()
    {
        $reversed_adjacencyList = array_fill_keys($this->vertices, array());
        foreach($this->edges as [$source, $destination])
        {
            $reversed_adjacencyList[$destination][] = $source;
        }
        return $reversed_adjacencyList;
    }

    // Graph traversal

    public function BFS($vertex)
    {
        $queue[] = $vertex;
        $visited_vertices[$vertex] = true;

        while(!empty($queue))
        {
            $vertex = array_shift($queue);
            echo $vertex . " ";

            foreach($this->data[$vertex] as $neighbor)
            {
                if (isset($visited_vertices[$neighbor])) continue;
                $queue[] = $neighbor;
                $visited_vertices[$neighbor] = true;
            }
        }
    }

    public function DFS($starting_vertex, &$visited_vertices = [])
    {
        $stack = [$starting_vertex];
        $visited_vertices[$starting_vertex] = true;

        while(!empty($stack))
        {
            $vertex = array_pop($stack);
            $result[] = $vertex;
            foreach($this->data[$vertex] as $neighbor)
            {
                if (isset($visited_vertices[$neighbor])) continue;
                $stack[] = $neighbor;
                $visited_vertices[$neighbor] = true;
            }    
        }
        return $result;
    }

    // Cycle detection

    public function cyclesExist($graph)
    {
        foreach($graph as $vertex => $edges)
        {
            if ($this->cyclesExistHelper($graph, $vertex)) return true;
        }
        return false;
    }

    public function cyclesExistHelper($graph, $vertex, &$path = [], &$visited_vertices = [])
    {
        if (in_array($vertex, $path)) return true;
        if (isset($visited_vertices[$vertex]) && $visited_vertices[$vertex] === true) return false;
        $path[] = $vertex;
        $visited_vertices[$vertex] = true;

        foreach($graph[$vertex] as $neighbor)
        {
            if ($this->cyclesExistHelper($graph, $neighbor, $path, $visited_vertices)) return true;
        }
        array_pop($path);
        return false;
    }

    // Shortest path algorithms

    public function dijkstra($starting_vertex)
    {
        $shortest_distances = new priority_queue();
        $shortest_distances->enqueue([0, $starting_vertex]);
        $visited_vertices = [];

        foreach($this->vertices as $vertex)
        {
            if ($vertex === $starting_vertex) continue;
            $shortest_distances->enqueue([INF, $vertex]);
        }

        while (count($this->vertices) !== count($visited_vertices))
        {
            $current_vertex = $shortest_distances->dequeue();

            foreach($this->data[$current_vertex['value']] as [$weight, $neighbor])
            {
                if (isset($visited_vertices[$neighbor])) continue;

                // Find the neighbor index in the shortest distances priority queue
                for ($i = 0; $i < count($shortest_distances->data); $i++)
                {
                    if ($shortest_distances->data[$i]['value'] !== $neighbor) continue;
                    $neighbor_index = $i; 
                }

                if ($current_vertex['priority'] + $weight < $shortest_distances->data[$neighbor_index]['priority'])
                {
                    $shortest_distances->updatePriority($neighbor_index, $current_vertex['priority'] + $weight);
                }
            }
            $visited_vertices[$current_vertex['value']] = true;
            $shortest_distances_array[$current_vertex['value']] = $current_vertex['priority'];
        }

        return $shortest_distances_array;
    }

    public function bellman_ford($starting_vertex)
    {
        $shortest_distances = array_fill_keys($this->vertices, INF);
        $shortest_distances[$starting_vertex] = 0;

        // Relax all the graph edges V - 1 times where V is the number of vertices
        for($i = 0; $i < count($this->vertices) - 1; $i++)
        {
            foreach($this->edges as [$source, $destination, $weight])
            {
                $shortest_distances[$destination] = min($shortest_distances[$destination], $shortest_distances[$source] + $weight);
            }
        }
        
        // One more iteration to ensure that there are no negative cycles in the graph
        foreach($this->edges as [$source, $destination, $weight])
        {
            if ($shortest_distances[$source] + $weight < $shortest_distances[$destination])
            {
                throw new \Exception("Negative cycle detected");
            }
        }
        return $shortest_distances;
    }

    // Minimum spanning trees

    public function kruskal()
    {
        $graph_edges = new priority_queue();
        $Disjoint_Set = new Disjoint_Set();
        $MST = [];
        $number_of_vertices = $total_number_of_edges_in_the_MST = 0;

        foreach($this->data as $source => $edges)
        {
            $number_of_vertices++;
            // Build the structure of the MST
            $MST[$source] = [];
            $Disjoint_Set->parent[$source] = $source;
            foreach($edges as [$weight, $destination])            
            {
                // The edge already exists: Avoid duplicates
                $graph_edges->enqueue([$weight, [$source, $destination]]);
            }
        }
        // Fill the rank array in the disjoint set that is used to apply the union by rank mechanism
        $Disjoint_Set->rank = array_fill_keys($this->vertices, 1);

        while($total_number_of_edges_in_the_MST < $number_of_vertices - 1)
        {
            $current_smallest_weighted_edge = $graph_edges->dequeue();

            $source = $current_smallest_weighted_edge['value'][0];
            $destination = $current_smallest_weighted_edge['value'][1];
            $weight = $current_smallest_weighted_edge['priority'];

            // If the source and the destination vertices are at the same tree then adding the edge will form a cycle
            if ($Disjoint_Set->find($destination) === $Disjoint_Set->find($source)) continue;

            $MST[$source][] = [$weight, $destination];
            $MST[$destination][] = [$weight, $source];
            $Disjoint_Set->union($source, $destination);
            $total_number_of_edges_in_the_MST++;
        }
        return $MST;
    }

    public function prim()
    {
        $edges = new priority_queue();
        $MST = [];
        $total_number_of_edges_in_the_MST = 0;
        $arbitrary_vertex = $this->vertices[0];
        foreach($this->data[$arbitrary_vertex] as [$weight, $destination])
        {
            $edges->enqueue([$weight, [$arbitrary_vertex, $destination]]);
        }

        while(!empty($edges->data))
        {
            if($total_number_of_edges_in_the_MST === count($this->vertices) - 1) break;

            $minimum_weight_edge_in_the_mst = $edges->dequeue();
            $weight = $minimum_weight_edge_in_the_mst['priority'];
            $source = $minimum_weight_edge_in_the_mst['value'][0];
            $destination = $minimum_weight_edge_in_the_mst['value'][1];

            // If the vertex is already in the mst move on to the next edge
            if(isset($MST[$destination])) continue;

            // Add the edge to the MST 
            $MST[$source][] = [$weight, $destination];
            $MST[$destination][] = [$weight, $source];
            $total_number_of_edges_in_the_MST++;

            // Add the destination's edges to the edges of the MST
            foreach($this->data[$destination] as [$weight, $neighbor])
            {
                $edges->enqueue([$weight, [$destination, $neighbor]]);
            }
        }
        return $MST;
    }

    // Topological sorting

    public function topological_sort()
    {
        $stack = $visited_vertices = [];

        foreach($this->vertices as $vertex)
        {
            if (isset($visited_vertices[$vertex])) continue;
            $this->topological_sort_helper($vertex, $stack, $visited_vertices);
        }
        return array_reverse($stack);
    }

    public function topological_sort_helper($vertex, &$stack, &$visited_vertices)
    {
        // Vertex has already been visited
        if (isset($visited_vertices[$vertex])) return;
        $visited_vertices[$vertex] = true;
        foreach($this->data[$vertex] as $destination)
        {
            $this->topological_sort_helper($destination, $stack, $visited_vertices);
        }
        $stack[] = $vertex;
    }

    public function kahn()
    {
        $queue_storing_vertices_with_no_dependencies = $topological_sort = [];
        $vertices_number_of_dependencies = array_fill_keys($this->vertices, 0);
        foreach($this->edges as [$source, $destination])
        {
            $vertices_number_of_dependencies[$destination]++;
        }

        foreach($vertices_number_of_dependencies as $vertex => $in_degree)
        {
            if ($in_degree !== 0) continue;
            $queue_storing_vertices_with_no_dependencies[] = $vertex;
        }

        while(!empty($queue_storing_vertices_with_no_dependencies))
        {
            $vertex_with_no_dependencies = array_shift($queue_storing_vertices_with_no_dependencies);
            $topological_sort[] = $vertex_with_no_dependencies;
            foreach($this->data[$vertex_with_no_dependencies] as $neighbor)
            {
                $vertices_number_of_dependencies[$neighbor]--;
                if ($vertices_number_of_dependencies[$neighbor] !== 0) continue;
                $queue_storing_vertices_with_no_dependencies[] = $neighbor;
            }
        }

        return $topological_sort;
    }

    // Connectivity

    public function find_articulation_points()
    {
        $number_of_vertices = count($this->vertices);
        $articulation_points = [];
    
        /* Iterate over all the vertices removing a vertex and its associated edges one at a time, 
           if the removal operation disconnects the graph then the vertex is an articulation point */
        foreach($this->data as $vertex => $destinations)
        {
            // Remove the vertex and its associated edges
            $this->removeVertex($vertex);
            foreach($destinations as $destination)
            {
                $index = array_search($vertex, $this->data[$destination]);
                unset($this->data[$destination][$index]);
            }
    
            // Check whether the graph has been disconnected or not 
            if (count($this->DFS($this->vertices[array_rand($this->vertices)])) < $number_of_vertices - 1)
            {
                $articulation_points[] = $vertex;
            }
    
            // ReAdd the vertex and its associated edges
            $this->addVertex($vertex);
            $this->data[$vertex] = $destinations;
            foreach($destinations as $destination)
            {
                $this->data[$destination][] = $vertex;
            }
        }
        return $articulation_points;
    }

    public function find_bridges()
    {
        $number_of_vertices = count($this->vertices);
        $bridges = [];
    
        /* Iterate over all the vertices removing an edge one at a time, 
           if the removal operation disconnects the graph then the edge is a bridge */
        foreach($this->edges as [$source, $destination])
        {
            // Remove the edge from the graph
            $index0 = array_search($destination, $this->data[$source]);
            unset($this->data[$source][$index0]);
            $index1 = array_search($source, $this->data[$destination]);
            unset($this->data[$destination][$index1]);

            // Check whether the graph has been disconnected or not 
            if (count($this->DFS($this->vertices[array_rand($this->vertices)])) < $number_of_vertices)
            {
                $bridges[] = [$source, $destination];
            }
    
            // ReAdd the edge at the same indices for the sake of consistency 
            $this->data[$source][$index0] = $destination;
            $this->data[$destination][$index1] = $source;
        }
        return $bridges;
    }

    public function find_connected_components()
    {
        $visited_vertices = $connected_components = [];
        foreach($this->vertices as $vertex)
        {
            if (isset($visited_vertices[$vertex]) && $visited_vertices[$vertex] === true) continue;
            $connected_components[] =  $this->DFS($vertex, $visited_vertices);
        }
        return $connected_components;
    }

    public function find_SCCs_kosaraju()
    {
        // Initialize variables
        $stack = $SCCs = $Second_dfs_recursion_stack = $visited_vertices = [];

        // First dfs to create a stack that stores the vertices with high probability to form SCCs at the top 
        foreach($this->vertices as $vertex)
        {
            kosaraju_dfs1($this->data, $vertex, $stack, $visited_vertices);
        }

        // Getting the transpose of the graph
        $transpose_graph = $this->transpose();

        // Reset the visited vertices array
        $visited_vertices = [];

        while (!empty($stack))
        {
            $vertex = array_pop($stack);
            if (isset($visited_vertices[$vertex])) continue;
            kosaraju_dfs2($transpose_graph, $vertex, $Second_dfs_recursion_stack, $visited_vertices);
            $SCCs[] = $Second_dfs_recursion_stack;
            $Second_dfs_recursion_stack = [];
        }

        return $SCCs;
    }

    public function find_SCCs_tarjan()
    {
        $SCCs = $stack = $disc = $low = [];
        $present_in_stack = array_fill_keys($this->vertices, false);
        $timer = 0;
        foreach($this->vertices as $vertex)
        {
            if (isset($disc[$vertex])) continue;
            $this->tarjan_dfs($vertex, $stack, $present_in_stack, $timer, $disc, $low, $SCCs);
        }
        return $SCCs;
    }

    public function tarjan_dfs($vertex, &$stack, &$present_in_stack, &$timer, &$disc, &$low, &$SCCs)
    {
        $disc[$vertex] = $low[$vertex] = $timer;
        $timer++;
        $stack[] = $vertex;
        $present_in_stack[$vertex] = true;

        foreach($this->data[$vertex] as $neighbor)
        {
            // If the vertex is not visited
            if  (!isset($disc[$neighbor]))
            {
                $this->tarjan_dfs($neighbor, $stack, $present_in_stack, $timer, $disc, $low, $SCCs);
                $low[$vertex] = min($low[$vertex], $low[$neighbor]);
            }
            else
            {
                // If the edge is a cross edge ignore it and move on to the next neighbor
                if ($present_in_stack[$neighbor] === false) continue;
                // Else then the edge is a back edge
                $low[$vertex] = min($low[$vertex], $disc[$neighbor]);
            }
        }
        if ($disc[$vertex] !== $low[$vertex]) return;
        $SCC = [];
        // Pop vertices off the stack till we reach the head of the strongly connected component
        while (true)
        {
            $vertex = array_pop($stack);
            $present_in_stack[$vertex] = false;
            $SCC[] = $vertex;
            if ($disc[$vertex] === $low[$vertex]) break;
        }
        $SCCs[] = $SCC;
    }

    public function bi_connected()
    {
        // If the graph is disconnected then return false
        if (count($this->DFS($this->vertices[array_rand($this->vertices)])) !== count($this->vertices)) return false;
        // If articulation points are present then return false
        if (count($this->find_articulation_points()) !== 0) return false;
        // Otherwise if the two properties are met return true
        return true;
    }
}

class Disjoint_Set
{
    public $parent = [];
    public $rank;

    public function find($element)
    {
        if ($this->parent[$element] === $element) return $element;
        
        $parent = $this->find($this->parent[$element]);

        $this->parent[$element] = $parent;

        return $parent;
    }

    public function union($element1, $element2)
    {
        $element1Rep = $this->find($element1);
        $element2Rep = $this->find($element2);

        $element1RepRank = $this->rank[$element1Rep];
        $element2RepRank = $this->rank[$element2Rep];

        if ($element1RepRank === $element2RepRank)
        {
            // Merge any tree into the other: I have chosen to merge 2 in 1. why? just because
            $this->parent[$element1Rep] = $element2Rep;
            // Increase the rank (more specifically the height) by one
            $this->rank[$element1Rep]++;
            return;
        }
        $this->parent[$element1Rep] = ($element1RepRank > $element2RepRank) ? $element2Rep : $element1Rep;
        $this->parent[$element2Rep] = ($element2RepRank > $element1RepRank) ? $element1Rep : $element2Rep;
    }
}

// Helping functions 

function kosaraju_dfs1($graph, $vertex, &$stack, &$visited_vertices)
{
    if (isset($visited_vertices[$vertex])) return;
    $visited_vertices[$vertex] = true;
    foreach($graph[$vertex] as $neighbor)
    {
        kosaraju_dfs1($graph, $neighbor, $stack, $visited_vertices);
    }
    $stack[] = $vertex;
}

function kosaraju_dfs2($transpose_graph, $vertex, &$Second_dfs_recursion_stack, &$visited_vertices)
{
    if (isset($visited_vertices[$vertex])) return;
    $visited_vertices[$vertex] = true;
    $Second_dfs_recursion_stack[] = $vertex;
    foreach($transpose_graph[$vertex] as $neighbor)
    {
        kosaraju_dfs2($transpose_graph, $neighbor, $Second_dfs_recursion_stack, $visited_vertices);
    }
}
