<h5>
This is a short project for shortening the url.
</h5>

<p>
    The Main page shows a form receiving an optional folder and an url from the users. After submitting the form system will generate a short url for the users. For example if a user give a url like http://example.com/folder/subfolder?q=something#hash ignoring the optional folder field the system can generate a unique short url like http://hostname/4Hs8kL. If the user give an optional folder name like 'myfolder', the short url can be like this - http://hostname/myfolder/4Hs8kL. User can use multilevel folder structure like folder/subfolder to get - http://hostname/folder/subfolder/4Hs8kL.
</p>

<h5>Initial work to run the project</h5>
<p>
1. Compser update<br>
2. Make an env file from example env file<br>
3. Rename the database name and create database with same filename<br>
4. php artisan migrate:fresh<br>
5. php artisan key:generate<br>
</p>