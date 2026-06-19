import React from 'react';
import { Platform, StyleSheet } from 'react-native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import HomeScreen from '../screens/HomeScreen';
import AcademicsHubScreen from '../screens/AcademicsHubScreen';
import ProfileScreen from '../screens/ProfileScreen';
import CoursesScreen from '../screens/CoursesScreen';
import AttendanceScreen from '../screens/AttendanceScreen';
import TransactionsScreen from '../screens/TransactionsScreen';
import AssignmentsHubScreen from '../screens/AssignmentsHubScreen';
import ContentSubjectsScreen from '../screens/ContentSubjectsScreen';
import ContentListScreen from '../screens/ContentListScreen';
import AssignmentDetailScreen from '../screens/AssignmentDetailScreen';
import AssignmentFileScreen from '../screens/AssignmentFileScreen';
import TestResultsScreen from '../screens/TestResultsScreen';
import ClassTestResultDetailScreen from '../screens/ClassTestResultDetailScreen';
import AccountHomeScreen from '../screens/AccountHomeScreen';
import ChangePasswordScreen from '../screens/ChangePasswordScreen';
import AppIcon from '../components/AppIcon';
import { PRIMARY } from '../config';
import { androidTabLabelStyle } from '../ui/typography';
import { resetStackOnTabPress } from './resetStackOnTabPress';
import { useTabBarStyle } from './useTabBarStyle';

const Tab = createBottomTabNavigator();
const Stack = createNativeStackNavigator();

const TAB_ICONS: Record<string, { active: string; inactive: string }> = {
  Home: { active: 'home', inactive: 'home-outline' },
  Academics: { active: 'school', inactive: 'school-outline' },
  Assignments: { active: 'document-text', inactive: 'document-text-outline' },
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

function AcademicsStack() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }} initialRouteName="AcademicsHub">
      <Stack.Screen name="AcademicsHub" component={AcademicsHubScreen} />
      <Stack.Screen name="Courses" component={CoursesScreen} />
      <Stack.Screen name="Attendance" component={AttendanceScreen} />
      <Stack.Screen name="Transactions" component={TransactionsScreen} />
      <Stack.Screen name="TestResults" component={TestResultsScreen} />
      <Stack.Screen name="AllTestMarks" children={() => <TestResultsScreen allMarks />} />
      <Stack.Screen name="ClassTestResultDetail" component={ClassTestResultDetailScreen} />
    </Stack.Navigator>
  );
}

function AssignmentsStack() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }} initialRouteName="AssignmentsHub">
      <Stack.Screen name="AssignmentsHub" component={AssignmentsHubScreen} />
      <Stack.Screen name="ContentSubjects" component={ContentSubjectsScreen} />
      <Stack.Screen name="ContentList" component={ContentListScreen} />
      <Stack.Screen name="AssignmentDetail" component={AssignmentDetailScreen} />
      <Stack.Screen name="AssignmentFile" component={AssignmentFileScreen} />
    </Stack.Navigator>
  );
}

function AccountStack() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }}>
      <Stack.Screen name="AccountHome" component={AccountHomeScreen} />
      <Stack.Screen name="Profile" component={ProfileScreen} />
      <Stack.Screen name="ChangePassword" component={ChangePasswordScreen} />
    </Stack.Navigator>
  );
}

export default function MainTabs() {
  const { tabBarStyle } = useTabBarStyle();

  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        headerShown: false,
        tabBarActiveTintColor: PRIMARY,
        tabBarInactiveTintColor: '#94a3b8',
        tabBarStyle,
        tabBarHideOnKeyboard: true,
        tabBarLabelStyle: androidTabLabelStyle,
        tabBarIconStyle: Platform.OS === 'android' ? styles.tabBarIconAndroid : undefined,
        tabBarIcon: ({ focused }) => <TabIcon label={route.name} focused={focused} />,
      })}>
      <Tab.Screen name="Home" component={HomeScreen} />
      <Tab.Screen
        name="Academics"
        component={AcademicsStack}
        options={{ title: 'Academics' }}
        listeners={resetStackOnTabPress()}
      />
      <Tab.Screen
        name="Assignments"
        component={AssignmentsStack}
        options={{ title: 'Work' }}
        listeners={resetStackOnTabPress()}
      />
      <Tab.Screen name="Account" component={AccountStack} listeners={resetStackOnTabPress()} />
    </Tab.Navigator>
  );
}

const styles = StyleSheet.create({
  tabBarIconAndroid: { marginTop: 2 },
});
