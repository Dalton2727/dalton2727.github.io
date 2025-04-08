import React, { useState, useEffect } from 'react';
import { View, Text, FlatList, Alert, StyleSheet } from 'react-native';

export default function MenuScreen() {
  const [menuData, setMenuData] = useState([]);
  const [loading, setLoading] = useState(true);

const fetchFromServer = async () => {
  console.log('fetchFromServer called');
  try {
    const response = await fetch('http://10.0.2.2/index2.php/user/list');
    console.log('Response received:', response.status);

    if (!response.ok) throw new Error(`HTTP status ${response.status}`);

    const data = await response.json();
    console.log('Menu Items:', data);

    setMenuData(data);
  } catch (error) {
    console.error('Fetch error:', error.message);
    Alert.alert('Error', error.message);
  } finally {
    setLoading(false);
  }
};

  useEffect(() => {
    fetchFromServer();
  }, []);

  return (
    <View style={styles.container}>
      <Text style={styles.header}>Menu Screen</Text>
      {loading ? (
        <Text>Loading...</Text>
      ) : (
        <FlatList
          data={menuData}
          keyExtractor={(item, index) => index.toString()}
          renderItem={({ item }) => (
            <Text style={styles.item}>{JSON.stringify(item)}</Text>
          )}
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 20,
    marginTop: 40,
  },
  header: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 10,
  },
  item: {
    padding: 10,
    borderBottomWidth: 1,
    borderColor: '#ccc',
  },
});
