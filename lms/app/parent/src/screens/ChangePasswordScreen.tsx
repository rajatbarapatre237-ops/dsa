import React, { useState } from 'react';
import { Text, TextInput, Pressable, StyleSheet, Alert } from 'react-native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';

export default function ChangePasswordScreen() {
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
    <ScreenLayout title="Change Password" subtitle="Account security">
      <Card>
        <Text style={styles.label}>Current password</Text>
        <TextInput style={styles.input} secureTextEntry value={current} onChangeText={setCurrent} />
        <Text style={styles.label}>New password</Text>
        <TextInput style={styles.input} secureTextEntry value={next} onChangeText={setNext} />
        <Pressable style={[styles.btn, loading && styles.disabled]} onPress={save} disabled={loading}>
          <Text style={styles.btnText}>{loading ? 'Saving…' : 'Update password'}</Text>
        </Pressable>
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  label: { fontWeight: '600', marginBottom: 6, marginTop: 8 },
  input: { borderWidth: 1, borderColor: '#e2e8f0', borderRadius: 10, padding: 12, marginBottom: 8 },
  btn: { backgroundColor: PRIMARY, padding: 14, borderRadius: 10, alignItems: 'center', marginTop: 12 },
  btnText: { color: '#fff', fontWeight: '700' },
  disabled: { opacity: 0.6 },
});
