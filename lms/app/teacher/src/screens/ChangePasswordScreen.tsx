import React, { useState } from 'react';
import { Text, TextInput, Pressable, StyleSheet, Alert } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { useThemeColors, textInputStyle } from '../ui/useThemeColors';
import { platformWeight } from '../ui/typography';

export default function ChangePasswordScreen() {
  const navigation = useNavigation<any>();
  const colors = useThemeColors();
  const [current, setCurrent] = useState('');
  const [next, setNext] = useState('');
  const [loading, setLoading] = useState(false);

  async function save() {
    if (!current || !next) {
      Alert.alert('Error', 'Fill all fields');
      return;
    }
    setLoading(true);
    try {
      await LmsApi.changePassword({ current_password: current, new_password: next });
      Alert.alert('Success', 'Password updated');
      setCurrent('');
      setNext('');
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Failed');
    } finally {
      setLoading(false);
    }
  }

  return (
    <ScreenLayout
      title="Change Password"
      subtitle="Account security"
      onBack={() => navigation.navigate('AccountHome')}>
      <Card>
        <Text style={[styles.label, { color: colors.text }]}>Current password</Text>
        <TextInput
          style={[textInputStyle(colors), styles.inputSpacing]}
          secureTextEntry
          value={current}
          onChangeText={setCurrent}
          placeholderTextColor={colors.muted}
          keyboardAppearance="light"
        />
        <Text style={[styles.label, { color: colors.text }]}>New password</Text>
        <TextInput
          style={[textInputStyle(colors), styles.inputSpacing]}
          secureTextEntry
          value={next}
          onChangeText={setNext}
          placeholderTextColor={colors.muted}
          keyboardAppearance="light"
        />
        <Pressable style={[styles.btn, loading && styles.disabled]} onPress={save} disabled={loading}>
          <Text style={styles.btnText}>{loading ? 'Saving…' : 'Update password'}</Text>
        </Pressable>
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  label: { ...platformWeight('600'), marginBottom: 6, marginTop: 8 },
  inputSpacing: { marginBottom: 8 },
  btn: { backgroundColor: PRIMARY, padding: 14, borderRadius: 10, alignItems: 'center', marginTop: 12 },
  btnText: { color: '#fff', ...platformWeight('700') },
  disabled: { opacity: 0.6 },
});
