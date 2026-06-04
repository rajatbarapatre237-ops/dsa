import React from 'react';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { MenuTile } from '../components/Card';
import { AcademicsStackParamList } from '../navigation/types';

export default function AcademicsHubScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<AcademicsStackParamList>>();

  return (
    <ScreenLayout title="Academics" subtitle="Courses & attendance">
      <MenuTile title="Courses" onPress={() => navigation.navigate('Courses')} />
      <MenuTile title="Attendance" onPress={() => navigation.navigate('Attendance')} />
      <MenuTile title="Transactions" onPress={() => navigation.navigate('Transactions')} />
    </ScreenLayout>
  );
}
