import React, { useState, useEffect } from 'react';
import { View, Text, FlatList, Alert, StyleSheet } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { WebView } from 'react-native-webview';

const Tab = createBottomTabNavigator();

function ReviewsScreen() {
  const [reviewData, setReviewData] = useState([]);
  const [loading, setLoading] = useState(true);

const fetchFromServer = async () => {
  console.log('fetchFromServer called');
  try {
    const response = await fetch('http://10.0.2.2/index2.php/user/list');
    console.log('Response received:', response.status);

    if (!response.ok) throw new Error(`HTTP status ${response.status}`);

    const data = await response.json();
    console.log('Reviews:', data);

    setReviewData(data);
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
      <Text style={styles.header}>Review Screen</Text>
      {loading ? (
        <Text>Loading...</Text>
      ) : (
        reviewData.length > 0 ? (
          <FlatList
            data={reviewData}
            keyExtractor={(item, index) => index.toString()}
            renderItem={({ item }) => (
              <View style={styles.card}>
                <Text style={styles.username}>User: {item.username}</Text>
                <Text> Location: {item.location}</Text>
                <Text> Meal Item: {item.meal}</Text>
                <Text> Rating: {item.rating}/10</Text>
              </View>
            )}
          />
        ) : (
          <Text>No reviews available.</Text>
        )
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
  card: {
    backgroundColor: '#f9f9f9',
    padding: 15,
    marginVertical: 8,
    borderRadius: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  username: {
    fontWeight: 'bold',
    fontSize: 16,
    marginBottom: 4,
  },
});

const HomeScreen = () => {
  return (
    <WebView
      source={{ uri: 'http://10.0.2.2/start.html' }}
    />
  );
};

export default function App() {
  return (
    <NavigationContainer>
      <Tab.Navigator
        initialRouteName="Reviews"
        screenOptions={({ route }) => ({
          tabBarIcon: ({ focused, color}) => {
            let label;

            if (route.name === 'Home') {
              label = 'Home';
            } else if (route.name === 'Reviews') {
              label = 'Reviews';
            }

            return <Text style={{ color: focused ? '#f5a623' : color, fontSize: 16 }}>{label}</Text>;
          },
        })}
      >
        <Tab.Screen name="Home" component={HomeScreen} />
        <Tab.Screen name="Reviews" component={ReviewsScreen} />
      </Tab.Navigator>
    </NavigationContainer>
  );
}
