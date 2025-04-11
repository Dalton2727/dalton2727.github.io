import React, { useEffect, createContext, useState, useContext } from 'react';
import { View, Text, FlatList, Alert, StyleSheet, Button, TextInput } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { WebView } from 'react-native-webview';
import { Ionicons } from '@expo/vector-icons';



const Tab = createBottomTabNavigator();

const UserContext = createContext();

export const useUser = () => {
  return useContext(UserContext);
};

export const UserProvider = ({ children }) => {
  const [username, setUsername] = useState('');

  const login = (user) => {
    setUsername(user);
  };

  const logout = () => {
    setUsername('');
  };

  return (
    <UserContext.Provider value={{ username, login, logout }}>
      {children}
    </UserContext.Provider>
  );
};


function ReviewsScreen() {
  const [reviewData, setReviewData] = useState([]);
  const [loading, setLoading] = useState(true);
  const { username } = useUser();
  const [editingReview, setEditingReview] = useState(null);
  const [location, setLocation] = useState('');
  const [meal, setMeal] = useState('');
  const [rating, setRating] = useState('');
  const [editForm, setEditForm] = useState({
    location: '',
    meal: '',
    rating: ''
  });

  //uses fetch command to fetch from server (calls from frontend to backend)
  const fetchFromServer = async () => {
    console.log('fetchFromServer called');
    try {
      const response = await fetch('http://10.0.2.2/index2.php/user/list', {
        headers: {
          'Content-Type': 'application/json',
        }
      });
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

const handleAddReview = async () => {
  //checks the user is logged in and all fields are filled in, otherwise returns an error
  if (!username) {
    Alert.alert('Error', 'You must be logged in to submit a review.');
    return;
  }

  if (!location || !meal || !rating) {
    Alert.alert('Error', 'Please fill in all fields.');
    return;
  }

  try {
    const response = await fetch('http://10.0.2.2/index2.php/user/addreview', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        username,
        location,
        meal,
        rating: parseInt(rating),
      }),
    });

    const text = await response.text();
    console.log('Raw response text:', text);

    let data;
    try {
      data = JSON.parse(text);
    } catch (parseError) {
      console.error('Failed to parse JSON:', parseError.message);
      Alert.alert('Error', 'Invalid JSON response from server.');
      return;
    }

    //review added to page if successful, page refreshed to reflect this
    if (data.success) {
      Alert.alert('Success', 'Review submitted');
      setLocation('');
      setMeal('');
      setRating('');
      fetchFromServer(); // refresh
    } else {
      throw new Error(data.message || 'Failed to add review');
    }
  } catch (error) {
    Alert.alert('Error', error.message || 'Failed to add review');
  }
};

// Defines handleDelete separately with similar format
const handleDelete = async (reviewId, reviewUsername) => {
  //checks the user is logged in as the right account before deleting reviews  
  try {
      if (!username) {
        Alert.alert('Error', 'You must be logged in to delete reviews');
        return;
      }

      if (username !== reviewUsername) {
        Alert.alert('Error', 'You can only delete your own reviews');
        return;
      }

      console.log('Attempting to delete review with ID:', reviewId, 'for user:', username);
      console.log('Current session username:', username);

      const response = await fetch('http://10.0.2.2/index2.php/user/delete', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        credentials: 'include',
        body: JSON.stringify({
          revid: reviewId,
          userid: username
        })
      });

      console.log('Delete response status:', response.status);
      console.log('Delete response headers:', response.headers);

      const responseText = await response.text();
      console.log('Raw response text:', responseText);

      let responseData;
      try {
        responseData = JSON.parse(responseText);
        console.log('Parsed response data:', responseData);
      } catch (parseError) {
        console.error('Failed to parse response as JSON:', parseError);
        throw new Error('Invalid response from server: ' + responseText);
      }

      if (!response.ok) {
        throw new Error(responseData.error || 'Failed to delete review');
      }

      if (responseData.success) {
        await fetchFromServer();
        Alert.alert('Success', 'Review deleted successfully');
      } else {
        throw new Error(responseData.error || 'Failed to delete review');
      }
    } catch (error) {
      console.error('Delete error:', error);
      Alert.alert('Error', error.message || 'Failed to delete review');
    }
  };
  //handleEdit function used when editing reviews
const handleEdit = async (reviewId, reviewUsername) => {
    try {
      if (!username) {
        Alert.alert('Error', 'You must be logged in to edit reviews');
        return;
      }

      if (username !== reviewUsername) {
        Alert.alert('Error', 'You can only edit your own reviews');
        return;
      }
      //also checks the rating is between 1 and 10 before letting them change rating
      const rating = parseInt(editForm.rating);
      if (isNaN(rating) || rating < 1 || rating > 10) {
        Alert.alert('Error', 'Rating must be between 1 and 10');
        return;
      }

      console.log('Attempting to edit review with ID:', reviewId, 'for user:', username);

      const response = await fetch('http://10.0.2.2/index2.php/user/edit', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        credentials: 'include',
        body: JSON.stringify({
          revid: reviewId,
          userid: username,
          location: editForm.location,
          meal: editForm.meal,
          rating: editForm.rating
        })
      });

      console.log('Edit response status:', response.status);

      const responseText = await response.text();
      console.log('Raw response text:', responseText);

      let responseData;
      try {
        responseData = JSON.parse(responseText);
        console.log('Parsed response data:', responseData);
      } catch (parseError) {
        console.error('Failed to parse response as JSON:', parseError);
        throw new Error('Invalid response from server: ' + responseText);
      }

      if (!response.ok) {
        throw new Error(responseData.error || 'Failed to edit review');
      }

      if (responseData.success) {
        await fetchFromServer();
        setEditingReview(null);
        setEditForm({ location: '', meal: '', rating: '' });
        Alert.alert('Success', 'Review edited successfully');
      } else {
        throw new Error(responseData.error || 'Failed to edit review');
      }
    } catch (error) {
      console.error('Edit error:', error);
      Alert.alert('Error', error.message || 'Failed to edit review');
    }
  };

  //makes sure user is logged in before allowing edit functionality
  //This way the user knows they are logged in/out and permissions are adjusted accordingly before even getting to the edit screen
  const startEditing = (review) => {
    if (!username) {
      Alert.alert('Error', 'You must be logged in to edit reviews');
      return;
    }

    if (username !== review.username) {
      Alert.alert('Error', 'You can only edit your own reviews');
      return;
    }

    setEditingReview(review.id);
    setEditForm({
      location: review.location,
      meal: review.meal,
      rating: review.rating
    });
  };

  //if the user cancels, resets edit form to default
  const cancelEditing = () => {
    setEditingReview(null);
    setEditForm({ location: '', meal: '', rating: '' });
  };

  useEffect(() => {
    fetchFromServer();
  }, []);

  //view of the application
  return (
    <View style={styles.container}>
      <Text style={styles.header}>Review Screen</Text>
      {username && <Text style={styles.header}>User: {username}</Text>}
      {loading ? (
        <Text>Loading...</Text>
      ) : (
        reviewData.length > 0 ? (
          <FlatList
            data={reviewData}
            keyExtractor={(item, index) => index.toString()}
            renderItem={({ item }) => (
              <View style={styles.card}>
                {editingReview === item.id ? (
                  <View>
                    <TextInput
                      style={styles.input}
                      placeholder="Location"
                      value={editForm.location}
                      onChangeText={(text) => setEditForm({ ...editForm, location: text })}
                    />
                    <TextInput
                      style={styles.input}
                      placeholder="Meal"
                      value={editForm.meal}
                      onChangeText={(text) => setEditForm({ ...editForm, meal: text })}
                    />
                    <TextInput
                      style={styles.input}
                      placeholder="Rating (1-10)"
                      value={editForm.rating}
                      onChangeText={(text) => {
                        const num = parseInt(text);
                        if (text === '' || (num >= 1 && num <= 10)) {//restricts ratings from 1-10
                          setEditForm({ ...editForm, rating: text });
                        }
                      }}
                      keyboardType="numeric"
                      maxLength={2}
                    />
                    <View style={{ flexDirection: 'row', justifyContent: 'space-between', marginTop: 4 }}>
                      <Button
                        title="Save"
                        onPress={() => handleEdit(item.id, item.username)}
                        color="#841584"
                      />
                      <Button
                        title="Cancel"
                        onPress={cancelEditing}
                        color="#666"
                      />
                    </View>
                  </View>
                ) : (
                  <View>
                    <Text style={styles.username}>User: {item.username}</Text>
                    <Text>Location: {item.location}</Text>
                    <Text>Meal Item: {item.meal}</Text>
                    <View style={styles.ratingRow}>
                      <Text>Rating: </Text>
                      {[...Array(10)].map((_, i) => {
                        //uses icons for rating system 1-10
                        let iconName = 'fast-food-outline';
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
                    </View>
                    <View style={{ flexDirection: 'row', marginTop: 4 }}> 
                      <Button
                        title="Edit" //edit and delete buttons are visible on each review so the user can easily choose which one they'd like to edit or delete
                        onPress={() => startEditing(item)}
                        color="#841584"
                        style={{ marginRight: 0 }}
                      />
                      <Button
                        title="Delete"
                        onPress={() => handleDelete(item.id, item.username)}
                        color="#841584"
                      />
                    </View>
                  </View>
                )}
              </View>
            )}
          />
        ) : (
          <Text>No reviews available.</Text>
        )
      )}
     {username ? (
//add review is available in reviews page if the user is logged in (posts the new review under that user)
       <View style={styles.card}>
         <Text style={styles.header}>Add a New Review</Text>
         <TextInput
           style={styles.input}
           placeholder="Location"
           value={location}
           onChangeText={setLocation}
         />
         <TextInput
           style={styles.input}
           placeholder="Meal"
           value={meal}
           onChangeText={setMeal}
         />
         <TextInput
           style={styles.input}
           placeholder="Rating (1-10)"
           value={rating}
           onChangeText={(text) => {
             const num = parseInt(text);
             if (text === '' || (num >= 1 && num <= 10)) {
               setRating(text);
             }
           }}
           keyboardType="numeric"
           maxLength={2}
         />
         <Button
           title="Submit Review"
           onPress={handleAddReview}
           color="#841584"
         />
       </View>
     ) : (
       <Text style={{ color: 'red' }}>Please log in to submit a review.</Text>
     )}

    </View>
  );
}


const LoginScreen = ({ navigation }) => {
  const [inputUsername, setInputUsername] = useState(''); // Local username input state
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const { username, login, logout } = useUser(); // Access username from context


  const handleLogin = async () => {
    setLoading(true);

    try {
      const response = await fetch('http://10.0.2.2/index2.php/user/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username: inputUsername, password }),
      });

      const data = await response.json();
      console.log(data);

      if (data.success) {
        login(data.username); // Set the username in context after successful login
        Alert.alert('Login successful', `Welcome ${data.username}`);
        navigation.navigate('Reviews');
      } else {
        Alert.alert('Login failed', data.message || 'Invalid credentials');
      }
    } catch (error) {
      console.error('Login error:', error.message);
      Alert.alert('Error', 'There was an issue with the login request.');
    } finally {
      setLoading(false);
    }
  };

  const handleLogout = () => {
    logout(); // Call logout function from context
    Alert.alert('Logged out', 'You have been logged out.');
  };

  return (
    <View style={styles.container}>
      <Text style={styles.header}>Login</Text>
      {username ? (
        <Text style={styles.header}>Currently logged in as: {username}</Text> // Show username if logged in
      ) : (
        <>
          {/* Username Input - Only shown before login */}
          <TextInput
            style={styles.input}
            placeholder="Username"
            value={inputUsername}
            onChangeText={setInputUsername}
          />

          {/* Password Input */}
          <TextInput
            style={styles.input}
            placeholder="Password"
            secureTextEntry
            value={password}
            onChangeText={setPassword}
          />

          {/* Login Button */}
          <Button
            title={loading ? 'Logging in...' : 'Login'}
            onPress={handleLogin}
            disabled={loading}
          />
        </>
      )}

      {/* Optionally, a register button */}
      {!username && (
        <Button
          title="Don't have an account? Sign up"
          onPress={() => navigation.navigate('Sign Up')}
          color="gray"
        />
      )}

      {/* Logout Button - Only shown if the user is logged in */}
      {username && (
        <Button
          title="Logout"
          onPress={handleLogout}
          color="#841584"
        />
      )}
    </View>
  );
};

const SignUpScreen = ({ navigation }) => {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSignUp = async () => {
    if (password !== confirmPassword) {
      Alert.alert('Error', 'Passwords do not match');
      return;
    }

    if (password.length < 10) {
       Alert.alert('Error', 'Password must be at least 10 characters long');
       return;
    }

    setLoading(true);

    try {
      const response = await fetch('http://10.0.2.2/index2.php/user/signup', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          username,
          password,
        }),
      });

      // Check if the response status is OK (status code 2xx)
      if (!response.ok) {
        throw new Error(`Server responded with status: ${response.status}`);
      }

      // Log the raw response to check what the server is sending back
      const responseText = await response.text();  // Get raw response as text
      console.log('Response Text:', responseText);

      // Now try to parse it as JSON
      const data = JSON.parse(responseText);  // Manually parse the text to JSON

      console.log('SignUp Response:', data);

      if (data.success) {
        Alert.alert('Sign Up Successful', 'You can now log in.');
        navigation.navigate('Reviews');
      } else {
        Alert.alert('Sign Up Failed', data.message || 'There was an error during sign up');
      }
    } catch (error) {
      // Check if the error has a message, otherwise log the entire error object
      if (error instanceof Error) {
        console.error('Sign Up Error:', error.message);
      } else {
        console.error('Sign Up Error:', error);  // Log the full error if it’s not an instance of Error
      }
      Alert.alert('Error', 'There was an issue with the sign-up request.');
    } finally {
      setLoading(false);
    }
  };


  return (
    <View style={styles.container}>
      <Text style={styles.header}>Sign Up</Text>

      {/* Username Input */}
      <TextInput
        style={styles.input}
        placeholder="Username"
        value={username}
        onChangeText={setUsername}
      />

      {/* Password Input */}
      <TextInput
        style={styles.input}
        placeholder="Password"
        secureTextEntry
        value={password}
        onChangeText={setPassword}
      />

      {/* Confirm Password Input */}
      <TextInput
        style={styles.input}
        placeholder="Confirm Password"
        secureTextEntry
        value={confirmPassword}
        onChangeText={setConfirmPassword}
      />

      {/* Sign Up Button */}
      <Button
        title={loading ? 'Signing Up...' : 'Sign Up'}
        onPress={handleSignUp}
        disabled={loading}
      />

      {/* Navigate to Login */}
      <Button
        title="Already have an account? Login"
        onPress={() => navigation.navigate('Login/Out')}
        color="gray"
      />
    </View>
  );
};



const HomeScreen = () => {
  return (
    <WebView source={{ uri: 'http://10.0.2.2/start1.html' }} />
  );
};

const AboutScreen = () => {
  return (
    <WebView source={{ uri: 'http://10.0.2.2/about1.html' }} />
  );
};

const App = () => {
  return (
  <UserProvider>
    <NavigationContainer>
      <Tab.Navigator initialRouteName="Reviews">
        <Tab.Screen
          name="Home"
          component={HomeScreen}
          options={{
            tabBarIcon: ({ focused, color, size }) => (
              <Ionicons name={focused ? 'home' : 'home-outline'} size={size} color={color} />
            ),
          }}
        />
        <Tab.Screen
          name="Reviews"
          component={ReviewsScreen}
          options={{
            tabBarIcon: ({ focused, color, size }) => (
              <Ionicons name={focused ? 'chatbubbles' : 'chatbubbles-outline'} size={size} color={color} />
            ),
          }}
        />
        <Tab.Screen
          name="About"
          component={AboutScreen}
          options={{
            tabBarIcon: ({ focused, color, size }) => (
              <Ionicons name={focused ? 'bulb' : 'bulb-outline'} size={size} color={color} />
            ),
          }}
        />
        <Tab.Screen
          name="Login/Out"
          component={LoginScreen}
          options={{
            tabBarIcon: ({ focused, color, size }) => (
              <Ionicons name={focused ? 'log-in' : 'log-in-outline'} size={size} color={color} />
            ),
          }}
        />
          <Tab.Screen
            name="Sign Up"
            component={SignUpScreen} // Add the SignUpScreen to the navigator
            options={{
              tabBarIcon: ({ focused, color, size }) => (
                <Ionicons name={focused ? 'person-add' : 'person-add-outline'} size={size} color={color} />
              ),
            }}
          />
      </Tab.Navigator>
    </NavigationContainer>
   </UserProvider>
  );
};

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
  input: {
    height: 40,
    borderColor: '#ccc',
    borderWidth: 1,
    marginBottom: 12,
    paddingHorizontal: 10,
    borderRadius: 4,
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

export default App;