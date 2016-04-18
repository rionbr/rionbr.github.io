#!encode utf-8
#
# Author: Rion Brattig Correia
# Date: March 14, 2016
# Description: A script that generates a network (using networkx)
# and exports to be graphml to be loaded into gephi for plotting
#

import networkx as nx

# Generate a Graph
G = nx.karate_club_graph()

# I want a boolean value for an edge
G.node[2]['connector_guy'] = 1

# Calculate Clustering
c_values = nx.clustering(G)
nx.set_node_attributes(G, 'cs', c_values)

# Calculate Betweeness
b_values = nx.betweenness_centrality(G)
nx.set_node_attributes(G, 'bs', b_values)

# Give Nodes the appropriate Name
labels = { i : 'node %d' % i for i in G.nodes()}
nx.set_node_attributes(G, 'label', labels)

# Print it
print 'Nodes:'
for g,d in G.nodes(data=True):
	print g, d
print 'Edges:', G.edges(data=True)

# Write graphml file
nx.write_graphml(G, 'gephi_graph.graphml')

# Write CSV files
# Edges
import pandas as pd
dfE = pd.DataFrame(G.edges(), columns=['Source','Target'])
dfE.to_csv('gephi_edges.csv', encoding='utf-8', index=False)
# Nodes
tmp = [ (n,d['label'],1 if 'connector_guy' in d else 0,d['cs'],d['bs']) for n,d in G.nodes(data=True)]
dfN = pd.DataFrame(tmp , columns=['Id','Label','connector_guy','cs','bs'])
dfN.to_csv('gephi_nodes.csv', encoding='utf-8', index=False)
