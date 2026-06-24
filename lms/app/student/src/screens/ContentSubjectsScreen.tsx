import React, { useCallback, useState } from 'react';
import { Text, StyleSheet, Pressable, View } from 'react-native';
import { useNavigation, useRoute, RouteProp } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import AppIcon from '../components/AppIcon';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';
import { AssignmentsStackParamList } from '../navigation/types';
import { useStaleLoad } from '../hooks/useStaleLoad';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';

type SubjectRow = {
  id?: number | null;
  subject_name: string;
  item_count: number;
};

export default function ContentSubjectsScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<AssignmentsStackParamList>>();
  const route = useRoute<RouteProp<AssignmentsStackParamList, 'ContentSubjects'>>();
  const contentKind = route.params.contentKind;
  const isNote = contentKind === 'note';

  const { loading, refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [subjects, setSubjects] = useState<SubjectRow[]>([]);

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const res: any = await LmsApi.contentSubjects(contentKind);
        setSubjects((res.subjects ?? []).filter((s: SubjectRow) => s.item_count > 0));
        markHasData();
      } finally {
        endLoad();
      }
    },
    [beginLoad, contentKind, endLoad, markHasData],
  );

  useRefreshOnFocus(load);

  const title = isNote ? 'Notes by subject' : 'Assignments by subject';

  return (
    <ScreenLayout
      title={title}
      subtitle="Choose a subject to view items"
      onBack={() => navigation.navigate('AssignmentsHub')}
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      <View style={styles.listCard}>
        {subjects.length > 0 ? (
          subjects.map((subject, index) => (
            <Pressable
              key={`${subject.id ?? 'general'}-${subject.subject_name}`}
              style={[styles.row, index > 0 && styles.rowBorder]}
              onPress={() =>
                navigation.navigate('ContentList', {
                  contentKind,
                  subjectId: subject.id,
                  subjectName: subject.subject_name,
                })
              }>
              <View style={styles.iconWrap}>
                <AppIcon name="book-outline" size={20} color={PRIMARY} />
              </View>
              <View style={styles.body}>
                <Text style={styles.name}>{subject.subject_name}</Text>
                <Text style={styles.meta}>
                  {subject.item_count} {isNote ? 'note' : 'assignment'}
                  {subject.item_count === 1 ? '' : 's'}
                </Text>
              </View>
              <AppIcon name="chevron-forward" size={18} color={theme.muted} />
            </Pressable>
          ))
        ) : !loading ? (
          <Text style={styles.empty}>
            {isNote ? 'No notes available yet' : 'No assignments available yet'}
          </Text>
        ) : null}
      </View>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  listCard: {
    backgroundColor: theme.card,
    borderRadius: 20,
    paddingHorizontal: 16,
    paddingVertical: 4,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 14,
    gap: 10,
  },
  rowBorder: {
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: theme.border,
  },
  iconWrap: {
    width: 40,
    height: 40,
    borderRadius: 12,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
  },
  body: { flex: 1 },
  name: { fontSize: 15, fontWeight: '800', color: theme.text },
  meta: { fontSize: 12, color: theme.muted, marginTop: 3 },
  empty: {
    fontSize: 14,
    color: theme.muted,
    textAlign: 'center',
    paddingVertical: 24,
  },
});
