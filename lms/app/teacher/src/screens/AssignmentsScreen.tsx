import React, { useCallback, useState } from 'react';
import { Text, StyleSheet, Pressable, View, Switch, Alert } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import ListRow from '../components/ListRow';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { WorkStackParamList } from '../navigation/types';

export default function AssignmentsScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<WorkStackParamList>>();
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState<any[]>([]);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = await LmsApi.assignments();
      setItems(res.assignments ?? []);
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  async function toggleStatus(item: any, on: boolean) {
    try {
      await LmsApi.updateAssignmentStatus(item.id, on);
      setItems(prev =>
        prev.map(a => (a.id === item.id ? { ...a, status: on ? 1 : 0 } : a)),
      );
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Could not update status');
    }
  }

  function confirmDelete(item: any) {
    Alert.alert('Delete assignment', `Remove "${item.document_name}"?`, [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Delete',
        style: 'destructive',
        onPress: async () => {
          try {
            await LmsApi.deleteAssignment(item.id);
            setItems(prev => prev.filter(a => a.id !== item.id));
          } catch (e: any) {
            Alert.alert('Error', e?.message ?? 'Could not delete');
          }
        },
      },
    ]);
  }

  return (
    <ScreenLayout
      title="Assignments"
      subtitle="Tap a row for details"
      onBack={() => navigation.navigate('WorkHub')}
      refreshing={loading}
      onRefresh={load}
      rightAction={
        <Pressable onPress={() => navigation.navigate('AddAssignment')}>
          <Text style={styles.addBtn}>+</Text>
        </Pressable>
      }>
      <Card>
        <DataList
          loading={loading}
          items={items}
          emptyText="No assignments yet"
          renderItem={(a: any) => (
            <View style={styles.item}>
              <ListRow
                title={a.document_name}
                subtitle={`${a.batch_name} · ${a.type}`}
                onPress={() => navigation.navigate('AssignmentDetail', { id: a.id })}
              />
              <View style={styles.row}>
                <Text style={styles.label}>Active</Text>
                <Switch
                  value={!!a.status}
                  onValueChange={v => toggleStatus(a, v)}
                  trackColor={{ true: PRIMARY }}
                />
              </View>
              <Pressable onPress={() => confirmDelete(a)}>
                <Text style={styles.delete}>Delete</Text>
              </Pressable>
            </View>
          )}
        />
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  addBtn: { color: '#fff', fontSize: 28, fontWeight: '300', lineHeight: 32 },
  item: { marginBottom: 4 },
  row: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 4 },
  label: { fontSize: 13, color: '#64748b' },
  delete: { color: '#dc2626', fontWeight: '600', marginBottom: 8 },
});
