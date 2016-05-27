<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * This code is copyright (c) Aliro Software Ltd - please see the notice in the 
 * index.php file for full details or visit http://aliro.org/copyright
 *
 * Some parts of Aliro are developed from other open source code, and for more 
 * information on this, please see the index.php file or visit 
 * http://aliro.org/credits
 *
 * Author: Martin Brampton
 * counterpoint@aliro.org
 *
 * aliroURIlink is an object that describes a connection between a URI and 
 * a class and method.
 */
 
 class aliroURIlink extends aliroDatabaseRow {
 	protected $DBclass = 'aliroCoreDatabase';
	protected $tableName = '#__urilinks';
	protected $rowKey = 'id';
 }