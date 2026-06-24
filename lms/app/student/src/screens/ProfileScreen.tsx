import React, { useCallback, useState } from 'react';
import { Text, StyleSheet, Alert, Pressable } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { FormInput } from '../components/FormInput';
import { LmsApi } from '../api/lms';
import { useStudentContext } from '../hooks/useStudentContext';
import { useStaleLoad } from '../hooks/useStaleLoad';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

export default function ProfileScreen() {
  const navigation = useNavigation<any>();
  const ctx = useStudentContext();
  const { refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [saving, setSaving] = useState(false);
  const [name, setName] = useState('');
  const [age, setAge] = useState('');
  const [mobile, setMobile] = useState('');
  const [school, setSchool] = useState('');
  const [email, setEmail] = useState('');
  const [city, setCity] = useState('');
  const [state, setState] = useState('');
  const [aadhar, setAadhar] = useState('');
  const [meta, setMeta] = useState<any>(null);

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const res: any = await LmsApi.profile();
        const p = res.profile ?? {};
        setMeta(p);
        setName(p.name ?? '');
        setAge(p.age ?? '');
        setMobile(p.mobile ?? '');
        setSchool(p.school_name ?? '');
        setEmail(p.email ?? '');
        setCity(p.city ?? '');
        setState(p.state ?? '');
        setAadhar(p.aadhar ?? '');
        markHasData();
      } finally {
        endLoad();
      }
    },
    [beginLoad, endLoad, markHasData],
  );

  useRefreshOnFocus(load);

  async function save() {
    if (!name.trim()) {
      Alert.alert('Required', 'Name is required');
      return;
    }
    setSaving(true);
    try {
      const res: any = await LmsApi.updateProfile({
        name: name.trim(),
        age,
        mobile,
        school_name: school,
        email,
        city,
        state,
        aadhar,
      });
      const updated = res.profile ?? {};
      setMeta(updated);
      await ctx.applyProfile(updated);
      Alert.alert('Saved', 'Profile updated');
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Could not save profile');
    } finally {
      setSaving(false);
    }
  }

  const m = meta ?? {};

  return (
    <ScreenLayout
      title="Edit Profile"
      subtitle="Your details"
      onBack={() => navigation.navigate('AccountHome')}
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      <Card>
        <Text style={styles.readonly}>Student ID: {ctx.studentId ?? m.id ?? '—'}</Text>
        <Text style={styles.readonly}>Course: {m.course_name ?? ctx.course ?? '—'}</Text>
        <Text style={styles.readonly}>Batch: {m.batch ?? ctx.batch ?? '—'}</Text>
        <Text style={styles.readonly}>Fees balance: {m.balance_fees ?? '—'}</Text>
        <FormInput label="Name" value={name} onChangeText={setName} />
        <FormInput label="Age" value={age} onChangeText={setAge} keyboardType="numeric" />
        <FormInput label="Mobile" value={mobile} onChangeText={setMobile} keyboardType="phone-pad" />
        <FormInput label="School" value={school} onChangeText={setSchool} />
        <FormInput
          label="Email"
          value={email}
          onChangeText={setEmail}
          autoCapitalize="none"
          keyboardType="email-address"
        />
        <FormInput label="City" value={city} onChangeText={setCity} />
        <FormInput label="State" value={state} onChangeText={setState} />
        <FormInput label="Aadhar" value={aadhar} onChangeText={setAadhar} />
        <Pressable style={[styles.saveBtn, saving && styles.disabled]} onPress={save} disabled={saving}>
          <Text style={styles.saveText}>{saving ? 'Saving…' : 'Save profile'}</Text>
        </Pressable>
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  readonly: { fontSize: 14, color: theme.muted, marginBottom: 8 },
  saveBtn: {
    backgroundColor: PRIMARY,
    paddingVertical: 14,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 12,
  },
  disabled: { opacity: 0.6 },
  saveText: { color: '#fff', fontWeight: '700', fontSize: 16 },
});
