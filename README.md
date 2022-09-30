-- Installation Steps
- composer install
- copy .env.example file and make .env and set database credentials
- php artisan migrate

-- API Document
- https://documenter.getpostman.com/view/23442747/2s83tCLDgh#e609239e-cce1-43f3-9d8d-2f963ba82f55
-- API End points
- Users

- user/lists
	=> Get All users list (except soft deleted)

- user/{user_id}
	=> Get user details by user_id

- user/add
	=> Create new user (username, email,password,default role User) with specific roles (1 - User, 2 - Moderator, 3 - Admin)
	
- user/{user_id}
	=> Update existing user by user_id

- user/{user_id} 
	=> soft delete specific user by user_id
	
- Task
	
- task/lists
=> Get All task list with search by ( status or assignee)

- task/{task_id}
=> Get task details by task_id

- task/add
=> Create new task (user as a assignee, name, description, default status New (1 - New, 2 - In Progress, 3 - On Review, 4 - Completed)) 

- task/{task_id}
=> Update specific task by task_id (assignee, name, description, status)

- task/{task_id}
=> soft delete specific task by task_id 
	
