import React from 'react';
import { StyleSheet } from 'react-native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import HomeScreen from '../screens/HomeScreen';
import AttendanceHubScreen from '../screens/AttendanceHubScreen';
import TodayAttendanceScreen from '../screens/TodayAttendanceScreen';
import MonthlyAttendanceScreen from '../screens/MonthlyAttendanceScreen';
import MarksHubScreen from '../screens/MarksHubScreen';
import TestResultsScreen from '../screens/TestResultsScreen';
import ClassTestResultDetailScreen from '../screens/ClassTestResultDetailScreen';
import AccountHomeScreen from '../screens/AccountHomeScreen';
import ChangePasswordScreen from '../screens/ChangePasswordScreen';
import AppIcon from '../components/AppIcon';
import { PRIMARY } from '../config';

const Tab = createBottomTabNavigator();
const Stack = createNativeStackNavigator();

const TAB_ICONS: Record<string, { active: string; inactive: string }> = {
  Home: { active: 'home', inactive: 'home-outline' },
  Attendance: { active: 'calendar', inactive: 'calendar-outline' },
  Marks: { active: 'stats-chart', inactive: 'stats-chart-outline' },
  Account: { active: 'person', inactive: 'person-outline' },
};

function TabIcon({ label, focused }: { label: string; focused: boolean }) {
  const icon = TAB_ICONS[label] ?? { active: 'ellipse', inactive: 'ellipse-outline' };

  return (
    <AppIcon
      name={focused ? icon.active : icon.inactive}
      size={22}
      color={focused ? PRIMARY : '#94a3b8'}
    />
  );
}

function AttendanceStack() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }} initialRouteName="AttendanceHub">
      <Stack.Screen name="AttendanceHub" component={AttendanceHubScreen} />
      <Stack.Screen name="TodayAttendance" component={TodayAttendanceScreen} />
      <Stack.Screen name="MonthlyAttendance" component={MonthlyAttendanceScreen} />
    </Stack.Navigator>
  );
}

function MarksStack() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }} initialRouteName="MarksHub">
      <Stack.Screen name="MarksHub" component={MarksHubScreen} />
      <Stack.Screen name="AllTestMarks" children={() => <TestResultsScreen allMarks />} />
      <Stack.Screen name="TestResults" component={TestResultsScreen} />
      <Stack.Screen name="ClassTestResultDetail" component={ClassTestResultDetailScreen} />
    </Stack.Navigator>
  );
}

function AccountStack() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }}>
      <Stack.Screen name="AccountHome" component={AccountHomeScreen} />
      <Stack.Screen name="ChangePassword" component={ChangePasswordScreen} />
    </Stack.Navigator>
  );
}

export default function MainTabs() {
  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        headerShown: false,
        tabBarActiveTintColor: PRIMARY,
        tabBarInactiveTintColor: '#94a3b8',
        tabBarStyle: styles.tabBar,
        tabBarIcon: ({ focused }) => <TabIcon label={route.name} focused={focused} />,
      })}>
      <Tab.Screen name="Home" component={HomeScreen} />
      <Tab.Screen name="Attendance" component={AttendanceStack} />
      <Tab.Screen name="Marks" component={MarksStack} options={{ title: 'Marks' }} />
      <Tab.Screen name="Account" component={AccountStack} />
    </Tab.Navigator>
  );
}

const styles = StyleSheet.create({
  tabBar: { height: 62, paddingBottom: 8, paddingTop: 6, backgroundColor: '#fff' },
});
