import React from 'react';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { MenuTile } from '../components/Card';
import { AttendanceStackParamList } from '../navigation/types';

export default function AttendanceHubScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<AttendanceStackParamList>>();

  return (
    <ScreenLayout title="Attendance" subtitle="Daily & monthly">
      <MenuTile title="Today's attendance" onPress={() => navigation.navigate('TodayAttendance')} />
      <MenuTile title="Monthly attendance" onPress={() => navigation.navigate('MonthlyAttendance')} />
    </ScreenLayout>
  );
}
