#!/bin/bash

# Make sure your JDK installation's bin directory is on your path:
export PATH=/path/to/jdk/bin:$PATH

# Set the directory where you put the source code and JAR files
export SE_HOME=/home/me/search_engine

# Now, set the CLASSPATH so we can run!
export CLASSPATH=$SE_HOME
export CLASSPATH=$CLASSPATH/$SE_HOME/lucene-1.2.jar
export CLASSPATH=$CLASSPATH/$SE_HOME/mysql-connectorj.jar

java org.ew.lucene.IndexEngine

