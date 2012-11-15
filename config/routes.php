<?php

/* Connect any route you want.
 * TadaApplicationRouter::addMap(match, controller, action, method);
 */
TadaApplicationRouter::addMap('/books/search', 'books', 'index');

/* Map a resource. This is mostly like on RoR.
 *
 * Resources are doing RESTful routing, only that you can customize
 * them by adding members, collections, namespace and other resources.
 */
TadaApplicationRouter::mapResource('books', array(
												'members' => array('preview' => 'GET'),
												'collections' => array('search' => 'GET'),
												// 'namespace' => 'admin',
												'resources' => array(
														'photos' => array(
															'namespace' => 'admin',
															'members' => array('test' => 'post')
																)
														)
											)
									);

// Map another resource.
TadaApplicationRouter::mapResource('flowers',
										array('members' => array('preview' => 'GET')));