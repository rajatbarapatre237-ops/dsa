import React from 'react';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { MenuTile } from '../components/Card';
import { AssignmentsStackParamList } from '../navigation/types';

export default function AssignmentsHubScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<AssignmentsStackParamList>>();

  return (
    <ScreenLayout title="Assignments" subtitle="Questions & tests">
      <MenuTile title="View assignments" onPress={() => navigation.navigate('AssignmentsList')} />
      <MenuTile title="Class test results" onPress={() => navigation.navigate('TestResults')} />
      <MenuTile title="All test marks" onPress={() => navigation.navigate('AllTestMarks')} />
    </ScreenLayout>
  );
}
