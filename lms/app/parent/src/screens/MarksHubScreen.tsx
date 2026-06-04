import React from 'react';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { MenuTile } from '../components/Card';
import { MarksStackParamList } from '../navigation/types';

export default function MarksHubScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<MarksStackParamList>>();

  return (
    <ScreenLayout title="Marks" subtitle="Class tests">
      <MenuTile title="All test marks" onPress={() => navigation.navigate('AllTestMarks')} />
      <MenuTile title="Class test results" onPress={() => navigation.navigate('TestResults')} />
    </ScreenLayout>
  );
}
