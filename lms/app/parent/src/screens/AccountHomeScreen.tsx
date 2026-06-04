import React from 'react';
import { Alert } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { MenuTile } from '../components/Card';
import { AppStorage } from '../storage/AppStorage';
import { AuthApi } from '../api/auth';
import { resetToLogin } from '../navigation/RootNavigation';

export default function AccountHomeScreen() {
  const navigation = useNavigation<any>();

  async function logout() {
    Alert.alert('Logout', 'Sign out of this device?', [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Logout',
        style: 'destructive',
        onPress: async () => {
          try {
            await AuthApi.logout();
          } catch {
            /* ignore */
          }
          await AppStorage.clear();
          resetToLogin();
        },
      },
    ]);
  }

  return (
    <ScreenLayout title="Account" subtitle="Security">
      <MenuTile title="Change password" onPress={() => navigation.navigate('ChangePassword')} />
      <MenuTile title="Logout" onPress={logout} />
    </ScreenLayout>
  );
}
