import React, { useCallback, useState } from 'react';
import { Text, StyleSheet, View } from 'react-native';
import { useNavigation, useRoute, RouteProp } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { SectionTitle } from '../components/DashboardUi';
import { AssignmentListItem, AssignmentsSummaryCard } from '../components/AssignmentUi';
import { LmsApi } from '../api/lms';
import { theme } from '../ui/theme';
import { AssignmentsStackParamList } from '../navigation/types';
import { useStaleLoad } from '../hooks/useStaleLoad';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';

export default function ContentListScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<AssignmentsStackParamList>>();
  const route = useRoute<RouteProp<AssignmentsStackParamList, 'ContentList'>>();
  const { contentKind, subjectId, subjectName } = route.params;
  const isNote = contentKind === 'note';

  const { loading, refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [items, setItems] = useState<any[]>([]);

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const res: any = await LmsApi.assignments({
          content_kind: contentKind,
          subject_id: subjectId ?? undefined,
          subject_name: subjectId ? undefined : subjectName,
        });
        const rows = res.assignments ?? [];
        setItems(rows);
        markHasData();
      } finally {
        endLoad();
      }
    },
    [beginLoad, contentKind, endLoad, markHasData, subjectId, subjectName],
  );

  useRefreshOnFocus(load);

  const title = `${subjectName} ${isNote ? 'notes' : 'assignments'}`;

  return (
    <ScreenLayout
      title={title}
      subtitle={isNote ? 'Class notes for this subject' : 'Homework for this subject'}
      onBack={() => navigation.navigate('ContentSubjects', { contentKind })}
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      <AssignmentsSummaryCard
        count={items.length}
        label={isNote ? 'Notes in this subject' : 'Assignments in this subject'}
      />

      <SectionTitle>{isNote ? 'All notes' : 'All assignments'}</SectionTitle>
      <View style={styles.listCard}>
        {items.length > 0 ? (
          items.map((item, index) => (
            <View key={item.id ?? index}>
              <AssignmentListItem
                item={item}
                onPress={() => navigation.navigate('AssignmentDetail', { id: item.id })}
              />
              {index < items.length - 1 ? <View style={styles.divider} /> : null}
            </View>
          ))
        ) : !loading ? (
          <Text style={styles.empty}>
            {isNote ? 'No notes in this subject yet' : 'No assignments in this subject yet'}
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
  divider: {
    height: StyleSheet.hairlineWidth,
    backgroundColor: theme.border,
  },
  empty: {
    fontSize: 14,
    color: theme.muted,
    textAlign: 'center',
    paddingVertical: 24,
  },
});
