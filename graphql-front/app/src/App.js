import React, {useEffect, useState} from 'react';
import {gql, useLazyQuery, useQuery} from "@apollo/client";
import axios from "axios";

function App() {
  const [x, setX] = useState();
  const [y, setY] = useState();

  const [message, setMessage] = useState('Guess what? Click the button');

  const query = gql(`
    query myData($episode: String) {
      hello
      hero(episode: $episode) 
    }
  `);

  const [get, { loading, error, data }]  = useLazyQuery(query);

  const sumHandler = () => {
    get({variables: {"episode": "testing..."}}).then(data => console.log(data.data)); //
  }

  return <>
    <p>Check console!!!</p>
    <button onClick={sumHandler}>Click me!</button>
  </>;
}

export default App;

/* TRY TO DO QUERY using AXIOS
let axiosConfig = {
  headers: {
    'Content-Type': 'application/json;charset=UTF-8',
  }
};

axios.post(
'http://localhost:1234',
{"query": "query myData($test: String) { hello hero(episode: $test)}" },
axiosConfig
)
.then((res) => {
  console.log('it`s working');
  console.log(res);

  setMessage(res.data.data.echo);
})
.catch((err) => {
  console.log("AXIOS ERROR: ", err);
})
*/