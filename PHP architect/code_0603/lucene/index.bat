@echo off

rem /* First make sure your JDK is in the path */
set PATH=c:\jdk_install_dir\bin;%PATH%

rem /* Set your search engine home directory */
rem /* this is where your source code is located, and where your */
rem /* dependant JAR files live!!! */
set SE_HOME=c:\search_engine

rem /* Now make sure the CLASSPATH contains the neccessary JAR files */
set CLASSPATH=%SE_HOME%
set CLASSPATH=%CLASSPATH%;%SE_HOME%\lucene-1.2.jar
set CLASSPATH=%CLASSPATH%;%SE_HOME%\mysql-connectorj.jar

java org.ew.lucene.IndexEngine

