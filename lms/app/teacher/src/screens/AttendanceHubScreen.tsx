import React from 'react';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { MenuTile } from '../components/Card';

export default function AttendanceHubScreen() {
  const navigation = useNavigation<any>();

  return (
    <ScreenLayout title="Attendance" subtitle="Mark and review">
      <MenuTile title="Add attendance" onPress={() => navigation.navigate('AddAttendance')} />
      <MenuTile title="View attendance" onPress={() => navigation.navigate('ViewAttendance')} />
    </ScreenLayout>
  );
}
