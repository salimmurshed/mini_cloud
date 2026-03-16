Have to migrate the migrations **php artisan  migrate**

Then **php artisan serve**

<a href="mini_cloud_collaction.postman_collection.json" download="mini_cloud_collaction.postman_collection.json">Postman Collection</a>




**Upload File**

POST [/users/{user_id}/files](/users/{user_id}/files)

 [/users/1/files](/users/1/files)

![alt text](image-1.png)


**Delete File**

DELETE [/users/{user_id}/files/{file_id}](/users/{user_id}/files/{file_id})

[/users/1/files/1](/users/1/files/1)

![alt text](image-2.png)


**Get Storage Summary**

GET [/users/{user_id}/storage-summary](/users/{user_id}/storage-summary)

[/users/1/storage-summary](/users/1/storage-summary)

![alt text](image-3.png)


**List User Files**

GET [/users/{user_id}/files](/users/{user_id}/files)

[/users/1/files](/users/1/files)

![alt text](image.png)
