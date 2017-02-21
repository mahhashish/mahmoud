#!/bin/bash

# NOTE: Before compiling these classes, make sure you change the relevant 
# details in them.  Things like database name/host/username/password and 
# index file path

# Make sure your JDK installation's bin directory is on your path:
export PATH=/path/to/jdk/bin:$PATH

# Set the directory where you put the source code and JAR files
export SE_HOME=/home/me/search_engine

# Now, set the CLASSPATH so we can compile!
export CLASSPATH=$SE_HOME/lucene-1.2.jar
export CLASSPATH=$CLASSPATH/$SE_HOME/mysql-connectorj.jar
export CLASSPATH=$CLASSPATH/$SE_HOME/wddx.jar

# JAR File locations (where you can download them):
# lucene: 
# 	http://jakarta.apache.org/builds/jakarta-lucene/release/v1.2/
#
# mysql-connectorj: 
#	http://www.mysql.com/downloads/api-jdbc-stable.html
#
# wddx:
#	http://www.openwddx.org/downloads/download.cfm
 

javac org/ew/lucene/*.java
