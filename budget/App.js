import React, { useState, useEffect } from 'react';
import { View, Text, FlatList, Alert, StyleSheet } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { WebView } from 'react-native-webview';
import { Ionicons } from '@expo/vector-icons';

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
                <View style={styles.ratingRow}>
                  <Text> Rating: </Text>
                  {[...Array(10)].map((_, i) => {
                    // Conditional check for location
                    let iconName = 'fast-food-outline'; // Default icon

                    if (item.location === 'RBC') {
                      iconName = i < item.rating ? 'cafe' : 'cafe-outline';
                    } else if (item.location === 'WesWings') {
                      iconName = i < item.rating ? 'fast-food' : 'fast-food-outline';
                    } else {
                      iconName = i < item.rating ? 'restaurant' : 'restaurant-outline'; 
                    }

                    return (
                      <Ionicons
                        key={i}
                        name={iconName}
                        size={18}
                        color={i < item.rating ? '#edc811' : '#ccc'}
                      />
                    );
                  })}
                  <Button
                    onPress={() =>
                      Alert.alert(
                      'Confirm Deletion',
                      'Are you sure you want to delete?',
                      [
                        {
                          text: 'No',
                          onPress: () => console.log('Delete cancelled'),
                          style: 'cancel',
                        },
                        {
                          text:'Yes',
                          onPress: () => console.log('Review deleted'),
                          style: 'cancel',
                        },
                      ],
                      { cancelable: true }
                      )
                    }
                    title="Edit"
                    color="#841584"
                    accessibilityLabel="Edit Button"
                  />
                  <Button
                  onPress={() =>
                    Alert.alert(
                    'Confirm Deletion',
                    'Are you sure you want to delete?',
                    [
                      {
                        text: 'No',
                        onPress: () => console.log('Delete cancelled'),
                          style: 'cancel',
                      },
                      {
                        text:'Yes',
                        onPress: () => console.log('Review deleted'),
                        style: 'cancel',
                      },
                    ],
                  { cancelable: true }
                    )
                  }
                    title="Delete"
                    color="#841584"
                    accessibilityLabel="Delete Button"
                  />
                </View>
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
  ratingRow: {
    flexDirection: 'row',
    marginTop: 4,
  }
});

const HomeScreen = () => {
  return (
    <WebView
      source={{ uri: 'http://10.0.2.2/start.html' }}
    />
  );
};

const AboutScreen = () => {
  return (
    <WebView
      source={{ uri: 'http://10.0.2.2/about.html' }}
    />
  );
};

const LoginScreen = () => {
  return (
    <WebView
      source={{ uri: 'http://10.0.2.2/index.php' }}
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
            let iconName;
            return <Ionicons name={iconName} size={size} color={color} />;
          },
        })}
      >
        <Tab.Screen name="Home" component={HomeScreen}
          options={{
            tabBarIcon: ({ focused, color, size }) => (
              <Ionicons name={focused ? 'home' : 'home-outline'} size={size} color={color} />
            ),
          }}
        />
        <Tab.Screen name="Reviews" component={ReviewsScreen}
        options={{
          tabBarIcon: ({ focused, color, size }) => (
            <Ionicons name={focused ? 'chatbubbles' : 'chatbubbles-outline'} size={size} color={color} />
          ),
        }}
      />
      <Tab.Screen name="About" component={AboutScreen}
        options={{
         tabBarIcon: ({focused, color, size }) => (
            <Ionicons name={focused ? 'bulb' : 'bulb-outline' } size={size} color={color} />
         ),
        }}
       />
      <Tab.Screen name="Login" component={LoginScreen}
        options={{
         tabBarIcon: ({focused, color, size }) => (
            <Ionicons name={focused ? 'log-in' : 'log-in-outline' } size={size} color={color} />
         ),
        }}
       />
      </Tab.Navigator>
    </NavigationContainer>
  );
}
