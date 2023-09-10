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
        unset($this->vertices[$vertex]);
    }

    public function setEdges($edges, $directed = false)
    {
        foreach($edges as [$source, $destination])
        {
            $this->data[$source][] = $destination; 
            $this->edges[] = [$source, $destination];
            if ($directed) continue;
            $this->data[$destination][] = $source; 
            $this->edges[] = [$source, $destination];
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
        foreach($this->data as $vertex => $edges)
        {
            $this->data[$vertex] = [];
            $this->edges = [];
        }
    }

    public function transpose($edges)
    {
        foreach($this->edges as [&$source, &$destination])
        {
            $temp = $source;
            $source = $destination;
            $destination = $source;
        }
        
        foreach($edges as [$source, $destination])
        {
            $index = array_search($destination, $this->data[$source]);
            unset($this->data[$source][$index]);
            // Use 1 after variable names to avoid collision
            $this->data[$destination][] = $source;
        }
    }

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

    public function DFS($starting_vertex)
    {
        $stack = [$starting_vertex];
        $visited_vertices[$starting_vertex] = true;

        while(!empty($stack))
        {
            $vertex = array_pop($stack);
            echo $vertex . " ";
            foreach($this->data[$vertex] as $neighbor)
            {
                if (isset($visited_vertices[$neighbor])) continue;
                $stack[] = $neighbor;
                $visited_vertices[$neighbor] = true;
            }    
        }
    }

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

    public function dijkstra($starting_vertex)
    {
        $shortest_distances = new priority_queue();
        $shortest_distances->enqueue([0, $starting_vertex]);

        foreach($this->vertices as $vertex)
        {
            $visited_vertices[$vertex] = false;
            if ($vertex === $starting_vertex) continue;
            $shortest_distances->enqueue([INF, $vertex]);
        }

        while (in_array(false, $visited_vertices))
        {
            $current_vertex = $shortest_distances->dequeue();

            foreach($this->data[$current_vertex['value']] as [$weight, $neighbor])
            {
                if (isset($visited_vertices[$neighbor]) && $visited_vertices[$neighbor] === true) continue;

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
        $shortest_distances[$starting_vertex] = $number_of_vertices = 0;

        foreach($this->vertices as $vertex)
        {
            $number_of_vertices++;
            if ($vertex === $starting_vertex) continue;
            $shortest_distances[$vertex] = INF;
        }

        // Relax all the graph edges V - 1 times where V is the number of vertices
        for($i = 0; $i < $number_of_vertices - 1; $i++)
        {
            foreach($this->edges as [$source, $destination, $weight])
            {
                $shortest_distances[$destination] = min($shortest_distances[$destination], $shortest_distances[$source] + $weight);
            }
        }
        
        // One more iteration to ensure that there are no negative cycles in the graph
        for ($i = 0; $i < 1; $i++)
        {
            foreach($this->edges as [$source, $destination, $weight])
            {
                if ($shortest_distances[$source] + $weight < $shortest_distances[$destination])
                {
                    throw new \Exception("Negative cycle detected");
                }
            }
        }
        return $shortest_distances;
    }

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
        // Choose an arbitrary vertex
        $arbitrary_source_vertex = $this->vertices[0];
        $edges = new priority_queue();
        $MST[$arbitrary_source_vertex] = [];
        $total_number_of_edges_in_the_MST = 0;

        // Add the arbitrary vertex's edges to the edges list
        foreach($this->data[$arbitrary_source_vertex] as [$weight, $destination])
        {
            $edges->enqueue([$weight, [$arbitrary_source_vertex, $destination]]);
        }

        while ($total_number_of_edges_in_the_MST < count($this->vertices) - 1)
        {
            $current_smallest_weighted_edge = $edges->dequeue();

            $weight = $current_smallest_weighted_edge['priority'];
            $source = $current_smallest_weighted_edge['value'][0];
            $destination = $current_smallest_weighted_edge['value'][1];

            // If the destination vertex is already in the MST: move on to the next edge
            if (isset($MST[$destination])) continue;

            // Add the edge to the MST
            $MST[$source][] = [$weight, $destination];
            $MST[$destination][] = [$weight, $source];

            // Add the destination vertex's edges to the edges priority queue
            foreach($this->data[$destination] as [$weight_of_the_edge, $destination_neighbor])
            {
                $edges->enqueue([$weight_of_the_edge, [$destination, $destination_neighbor]]);
            }

            // Keep track of the number of edges in the MST
            $total_number_of_edges_in_the_MST++;            
        }
        return $MST;
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
