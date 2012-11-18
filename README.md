# Afro
A PHP class for handling URL requests, similar to Ruby's [Sinatra gem](http://www.sinatrarb.com/).

# Working with Afro
Afro includes five procedural functions which allow you to handle requests with:

- `get`
- `post`
- `put`
- `delete`
- `ajax`

All of them take the same parameters but will only activate on the given request types.

## Handling a simple `GET` request with no parameters.

    get('/', function($Afro) {
        echo "HELLO";
    });

## Handling a `GET` request for getting a users name.

	get('/hello/(.*?)', function($Afro) {
		echo 'Hello ' . $Afro->param(2) . ', I hope today is full of Unicorns.'
	});

## Handling a `GET` request for getting a users name in different formats.
One of the beautiful things about Afro is that you can use the same request handler, but output different data depending on the format the request is called as.

Let's take the example above and use add a JSON output.

	get('/hello/(.*?)', function($Afro) {
		$Afro->format('json', function($Afro) {
            echo json_encode(array('name', $Afro->param(2)));
        });

        if(!$Afro->format)
			echo 'Hello ' . $Afro->param(2) . ', I hope today is full of Unicorns.'
	});

Now, if the request ends is `http://localhost/afro/hello/jbrooksuk.json` the output will be returned as a valid JSON string.

## Handling a simple POST request with a username.

	post('/connect/(.*?)', function($Afro) {
        if(!$Afro->format)
        	// Insert the user into a database? The format will always be the same in whichever function you use.
	});

# Author
- Blog: [james.brooks.so](http://james.brooks.so)
- Twitter: [@jbrooksuk](http://twitter.com/jbrooksuk)
- App.net: [@jbrooksuk](http://alpha.app.net/jbrooksuk)
- GitHub: [@jbrooksuk](http://github.com/jbrooksuk)

# Copyright
Copyright 2012 James Brooks

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this work except in compliance with the License. You may obtain a copy of the License in the LICENSE file, or at:

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.