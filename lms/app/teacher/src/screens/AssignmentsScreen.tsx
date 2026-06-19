import React, { useCallback, useMemo, useState } from 'react';
import { Text, StyleSheet, Pressable, View, Switch, Alert } from 'react-native';
import { useNavigation, useRoute, RouteProp } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import {
  AssignmentListItem,
  AssignmentsSummaryCard,
} from '../components/AssignmentUi';
import AppIcon from '../components/AppIcon';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';
import { platformWeight } from '../ui/typography';
import { WorkStackParamList } from '../navigation/types';
import { useStaleLoad } from '../hooks/useStaleLoad';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';

function isActiveStatus(status: unknown) {
  return status === 1 || status === '1' || status === true;
}

export default function AssignmentsScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<WorkStackParamList>>();
  const route = useRoute<RouteProp<WorkStackParamList, 'AssignmentsList' | 'NotesList'>>();
  const contentKind = route.name === 'NotesList' ? 'note' : 'assignment';
  const isNote = contentKind === 'note';

  const { loading, refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [items, setItems] = useState<any[]>([]);

  const title = isNote ? 'Notes' : 'Assignments';
  const addRoute = isNote ? 'AddNote' : 'AddAssignment';

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const res: any = await LmsApi.assignments({ content_kind: contentKind });
        setItems(res.assignments ?? []);
        markHasData();
      } finally {
        endLoad();
      }
    },
    [beginLoad, contentKind, endLoad, markHasData],
  );

  useRefreshOnFocus(() => load());

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
    Alert.alert(`Delete ${isNote ? 'note' : 'assignment'}`, `Remove "${item.document_name}"?`, [
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

  const activeCount = useMemo(
    () => items.filter(a => isActiveStatus(a.status)).length,
    [items],
  );

  return (
    <ScreenLayout
      title={title}
      subtitle={isNote ? 'Class notes by subject' : 'Homework and uploads by subject'}
      onBack={() => navigation.navigate('WorkHub')}
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}
      rightAction={
        <Pressable style={styles.addBtn} onPress={() => navigation.navigate(addRoute)}>
          <AppIcon name="add" size={22} color="#fff" />
        </Pressable>
      }>
      <AssignmentsSummaryCard count={activeCount} label={isNote ? 'Active notes' : undefined} />

      <Card title={`All ${title.toLowerCase()} (${items.length})`}>
        <DataList
          loading={loading}
          items={items}
          emptyText={isNote ? 'No notes yet' : 'No assignments yet'}
          renderItem={(a: any) => (
            <View style={[styles.item, items.indexOf(a) === items.length - 1 && styles.itemLast]}>
              <AssignmentListItem
                item={a}
                onPress={() => navigation.navigate('AssignmentDetail', { id: a.id })}
              />
              <View style={styles.itemFooter}>
                <View style={styles.toggleRow}>
                  <Text style={[styles.toggleLabel, platformWeight('600')]}>Visible to students</Text>
                  <Switch
                    value={isActiveStatus(a.status)}
                    onValueChange={v => toggleStatus(a, v)}
                    trackColor={{ true: PRIMARY, false: '#cbd5e1' }}
                  />
                </View>
                <Pressable style={styles.deleteBtn} onPress={() => confirmDelete(a)}>
                  <AppIcon name="trash-outline" size={16} color={theme.danger} />
                  <Text style={[styles.deleteText, platformWeight('700')]}>Delete</Text>
                </Pressable>
              </View>
            </View>
          )}
        />
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  addBtn: {
    width: 36,
    height: 36,
    borderRadius: 12,
    backgroundColor: PRIMARY,
    alignItems: 'center',
    justifyContent: 'center',
  },
  item: {
    marginBottom: 8,
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: theme.border,
    paddingBottom: 8,
  },
  itemLast: { borderBottomWidth: 0, marginBottom: 0, paddingBottom: 0 },
  itemFooter: {
    paddingLeft: 50,
    paddingRight: 4,
    gap: 8,
    marginTop: 4,
    marginBottom: 6,
  },
  toggleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  toggleLabel: { fontSize: 13, color: theme.muted },
  deleteBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    alignSelf: 'flex-start',
    paddingVertical: 4,
  },
  deleteText: { color: theme.danger, fontSize: 13 },
});
