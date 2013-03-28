<?php 

// Run tasks from the admin
class Decoy_Tasks_Controller extends Decoy_Base_Controller {
	
	// Configuration
	const MAX_EXECUTION_TIME = 600; // How long to allow a task to run for
	
	// Display all the seed tasks
	public function get_index() {
		
		// Store the tasks here
		$tasks = array();
		
		// Loop through all tasks
		$task_files = scandir(path('app').'tasks');
		foreach($task_files as $task_file) {
			if (!preg_match('#\w+\.php#', $task_file)) continue;
			
			// Get a list of all public seeding methods
			require_once(path('app').'tasks/'.$task_file);
			$task = basename($task_file, '.php');
			$class = "{$task}_Task";
			$instance = new $class();
			$methods = get_class_methods(self::class_name($task));
			
			// Check if the tasks should be ignored
			if (property_exists(self::class_name($task), 'IGNORE')) continue;
			
			// Filter some method names
			// __construct : This is typically only used for bootstrapping
			// worker : This is typicaly used by laravel workers (infinitely long running tasks)
			$methods_to_ignore = array('__construct', 'worker');
			foreach($methods_to_ignore as $method_name) {
				if (($key = array_search($method_name, $methods)) !== false) {
	    		unset($methods[$key]);
				}
			}
			
			// Create the task object
			$obj = (object) array(
				'methods' => $methods,
				'title' => \BKWLD\Utils\String::title_from_key($task),
				'description' => null,
			);
			
			// If the task inherits from Decoy\Task, use it's values
			if (is_a($instance, '\Decoy\Task')) {
				$obj->title = $instance->title();
				$obj->description = $instance->description();
			}
			
			// Add the methods to the listing
			$tasks[$task] = $obj;
		}
		
		// Render the view
		$this->layout->nest('content', 'decoy::tasks.index', array(
			'tasks' => $tasks
		));
	}
	
	// Run one of the methods, designed to be called via AJAX
	public function post_execute($task, $method_name) {

		// Make sure there is a seed task defined
		$path_to_task = path('app').'tasks/'.$task.'.php';
		if (!file_exists($path_to_task)) throw new Exception('This task could not be found');
		require_once($path_to_task);

		// Make sure the method is one of the defined ones
		$methods = get_class_methods(self::class_name($task));
		if (array_search($method_name, $methods) === false) throw new Exception('Task method does not exist');
		
		// Allow a really long execution time
		set_time_limit(self::MAX_EXECUTION_TIME);
		
		// Run the task and supress output
		ob_start();
		Laravel\CLI\Command::run(array($task.':'.$method_name));
		ob_end_clean();
		
		// Run the command
		return Response::json(null);
		
	}
	
	// Make the class name
	private static function class_name($task) {
		return str_replace(' ', '_', ucwords(str_replace('_', ' ', $task))).'_Task';
	}
	
}