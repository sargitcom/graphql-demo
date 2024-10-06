import React, {useEffect, useState} from 'react';
import {gql, useLazyQuery} from "@apollo/client";

function App() {
  const [x, setX] = useState();
  const [y, setY] = useState();

  const query = gql`{
      mutation sum(x, y): Integer
  }`;

  /*
  query GetPetPhoto($choice: String!) {
    pet(choice: $choice) {
      id
      imageURL
    }
  }
  */

  const [callQuery, { data, loading, error }] = useLazyQuery(query);

  const sumHandler = () => {

    callQuery({variables: {x, y}});

  }

  useEffect(
() => {
        console.log(data);
      },
[loading]
  );

  if (loading) return "Loading...";
  if (error) return <pre>{error.message}</pre>

  return <>
    <input type={"text"} name={"x"} value={x} onChange={event => setX(event.target.value)} />
    <input type={"text"} name={"y"} value={y} onChange={event => setY(event.target.value)} />
    <button onClick={sumHandler}>SUM</button>
  </>;
}

export default App;
