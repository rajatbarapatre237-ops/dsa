import React from 'react';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { MenuTile } from '../components/Card';
import { WorkStackParamList } from '../navigation/types';

export default function WorkHubScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<WorkStackParamList>>();

  return (
    <ScreenLayout title="Work" subtitle="Assignments & class tests">
      <MenuTile title="View assignments" subtitle="List, toggle, delete" onPress={() => navigation.navigate('AssignmentsList')} />
      <MenuTile title="Add assignment" subtitle="Link or file" onPress={() => navigation.navigate('AddAssignment')} />
      <MenuTile title="Class tests" subtitle="All tests" onPress={() => navigation.navigate('ClassTests')} />
      <MenuTile title="Create class test" onPress={() => navigation.navigate('CreateClassTest')} />
      <MenuTile title="Enter test marks" onPress={() => navigation.navigate('EnterMarks')} />
      <MenuTile title="Class test results" onPress={() => navigation.navigate('TestResults')} />
    </ScreenLayout>
  );
}
