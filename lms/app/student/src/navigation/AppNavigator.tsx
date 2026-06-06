import React, { useEffect, useState } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { isLoggedIn, subscribeAuth } from '../auth/authSession';
import LoginScreen from '../screens/LoginScreen';
import MainTabs from './MainTabs';
import { StudentContextProvider } from '../hooks/useStudentContext';
import { navigationRef } from './RootNavigation';
import { RootStackParamList } from './types';

const Stack = createNativeStackNavigator<RootStackParamList>();

export default function AppNavigator() {
  const [ready, setReady] = useState(false);
  const [loggedIn, setLoggedIn] = useState(false);

  useEffect(() => {
    let mounted = true;

    (async () => {
      const hasSession = await isLoggedIn();
      if (mounted) {
        setLoggedIn(hasSession);
        setReady(true);
      }
    })();

    const unsubscribe = subscribeAuth(setLoggedIn);

    return () => {
      mounted = false;
      unsubscribe();
    };
  }, []);

  if (!ready) {
    return null;
  }

  return (
    <NavigationContainer ref={navigationRef}>
      <Stack.Navigator screenOptions={{ headerShown: false }}>
        {loggedIn ? (
          <Stack.Screen name="Main">
            {() => (
              <StudentContextProvider>
                <MainTabs />
              </StudentContextProvider>
            )}
          </Stack.Screen>
        ) : (
          <Stack.Screen name="Login" component={LoginScreen} />
        )}
      </Stack.Navigator>
    </NavigationContainer>
  );
}
