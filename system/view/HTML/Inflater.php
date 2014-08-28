<?php
/**
 * Contains Inflater interface
 * 
 * @author Frederico Souza (fredericoamsouza@gmail.com)
 * @author Julio Cesar da Silva Pereira (thisjulio@gmail.com)
 * 
 * @copyright Copyright 2013 Frederico Souza
 * Licensed under the Apache License, Version 2.0 (the “License”);
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an “AS IS” BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 * Implements the Inflater pattern
 * @package system
 * @subpackage viewHtml
 */
interface Inflater{
		
    /**
     * Inflates an Element tree from a file
	 * 
     * @param string $layout HTML file path stored in application/view
     */
    static public function layoutInflater($layout);
}