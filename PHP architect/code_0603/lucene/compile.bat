@echo off

rem /* NOTE: Before compiling these classes, make sure you change the */
rem /* relevant details in them.  Things like database */
rem /* name/host/username/password and index file path */

rem /* First make sure your JDK is in the path */
set PATH=c:\jdk_install_dir\bin;%PATH%

rem /* Set your search engine home directory */
rem /* this is where your source code is located, and where your */
rem /* dependant JAR files live!!! */
set SE_HOME=c:\search_engine

rem /* Now make sure the CLASSPATH contains the neccessary JAR files */
set CLASSPATH=%SE_HOME%\lucene-1.2.jar
set CLASSPATH=%CLASSPATH%;%SE_HOME%\mysql-connectorj.jar
set CLASSPATH=%CLASSPATH%;%SE_HOME%\wddx.jar

rem # JAR File locations (where you can download them):
rem # lucene: 
rem # 	http://jakarta.apache.org/builds/jakarta-lucene/release/v1.2/
rem #
rem # mysql-connectorj: 
rem #	http://www.mysql.com/downloads/api-jdbc-stable.html
rem #
rem # wddx:
rem #	http://www.openwddx.org/downloads/download.cfm

javac org\ew\lucene\*.java


