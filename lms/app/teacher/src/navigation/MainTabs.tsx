import React from 'react';
import { Platform, StyleSheet } from 'react-native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import HomeScreen from '../screens/HomeScreen';
import StudentsScreen from '../screens/StudentsScreen';
import StudentDetailScreen from '../screens/StudentDetailScreen';
import AttendanceHubScreen from '../screens/AttendanceHubScreen';
import ViewAttendanceScreen from '../screens/ViewAttendanceScreen';
import AddAttendanceScreen from '../screens/AddAttendanceScreen';
import WorkHubScreen from '../screens/WorkHubScreen';
import AssignmentsScreen from '../screens/AssignmentsScreen';
import AddAssignmentScreen from '../screens/AddAssignmentScreen';
import AssignmentDetailScreen from '../screens/AssignmentDetailScreen';
import AssignmentFileScreen from '../screens/AssignmentFileScreen';
import ClassTestsScreen from '../screens/ClassTestsScreen';
import CreateClassTestScreen from '../screens/CreateClassTestScreen';
import EnterClassTestMarksScreen from '../screens/EnterClassTestMarksScreen';
import TestResultsScreen from '../screens/TestResultsScreen';
import ClassTestResultDetailScreen from '../screens/ClassTestResultDetailScreen';
import AccountHomeScreen from '../screens/AccountHomeScreen';
import SalaryScreen from '../screens/SalaryScreen';
import ChangePasswordScreen from '../screens/ChangePasswordScreen';
import AppIcon from '../components/AppIcon';
import { PRIMARY } from '../config';
import { androidTabLabelStyle } from '../ui/typography';

const Tab = createBottomTabNavigator();
const Stack = createNativeStackNavigator();

const TAB_ICONS: Record<string, { active: string; inactive: string }> = {
  Home: { active: 'home', inactive: 'home-outline' },
  Students: { active: 'people', inactive: 'people-outline' },
  Attendance: { active: 'calendar', inactive: 'calendar-outline' },
  Work: { active: 'document-text', inactive: 'document-text-outline' },
  Account: { active: 'settings', inactive: 'settings-outline' },
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

function StudentsStack() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }}>
      <Stack.Screen name="StudentsList" component={StudentsScreen} />
      <Stack.Screen name="StudentDetail" component={StudentDetailScreen} />
    </Stack.Navigator>
  );
}

function AttendanceStack() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }}>
      <Stack.Screen name="AttendanceHub" component={AttendanceHubScreen} />
      <Stack.Screen name="ViewAttendance" component={ViewAttendanceScreen} />
      <Stack.Screen name="AddAttendance" component={AddAttendanceScreen} />
    </Stack.Navigator>
  );
}

function WorkStack() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }} initialRouteName="WorkHub">
      <Stack.Screen name="WorkHub" component={WorkHubScreen} />
      <Stack.Screen name="AssignmentsList" component={AssignmentsScreen} />
      <Stack.Screen name="AddAssignment" component={AddAssignmentScreen} />
      <Stack.Screen name="AssignmentDetail" component={AssignmentDetailScreen} />
      <Stack.Screen name="AssignmentFile" component={AssignmentFileScreen} />
      <Stack.Screen name="ClassTests" component={ClassTestsScreen} />
      <Stack.Screen name="CreateClassTest" component={CreateClassTestScreen} />
      <Stack.Screen name="EnterMarks" component={EnterClassTestMarksScreen} />
      <Stack.Screen name="TestResults" component={TestResultsScreen} />
      <Stack.Screen name="ClassTestResultDetail" component={ClassTestResultDetailScreen} />
    </Stack.Navigator>
  );
}

function AccountStack() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }}>
      <Stack.Screen name="AccountHome" component={AccountHomeScreen} />
      <Stack.Screen name="Salary" component={SalaryScreen} />
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
        tabBarLabelStyle: androidTabLabelStyle,
        tabBarIconStyle: Platform.OS === 'android' ? styles.tabBarIconAndroid : undefined,
        tabBarIcon: ({ focused }) => <TabIcon label={route.name} focused={focused} />,
      })}>
      <Tab.Screen name="Home" component={HomeScreen} />
      <Tab.Screen name="Students" component={StudentsStack} />
      <Tab.Screen name="Attendance" component={AttendanceStack} />
      <Tab.Screen name="Work" component={WorkStack} options={{ title: 'Work' }} />
      <Tab.Screen name="Account" component={AccountStack} />
    </Tab.Navigator>
  );
}

const styles = StyleSheet.create({
  tabBar: {
    height: Platform.OS === 'android' ? 64 : 62,
    paddingBottom: Platform.OS === 'android' ? 10 : 8,
    paddingTop: 6,
    backgroundColor: '#fff',
  },
  tabBarIconAndroid: { marginTop: 2 },
});
