import React from 'react';
import { View, Text, FlatList, StyleSheet, ActivityIndicator } from 'react-native';
import { theme } from '../ui/theme';
import { PRIMARY } from '../config';

export function DataList({
  loading,
  items,
  emptyText,
  renderItem,
}: {
  loading: boolean;
  items: any[];
  emptyText: string;
  renderItem: (item: any) => React.ReactElement;
}) {
  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator color={PRIMARY} size="large" />
      </View>
    );
  }
  if (!items.length) {
    return (
      <View style={styles.center}>
        <Text style={styles.empty}>{emptyText}</Text>
      </View>
    );
  }
  return (
    <FlatList
      data={items}
      keyExtractor={(_, i) => String(i)}
      renderItem={({ item }) => renderItem(item)}
      scrollEnabled={false}
    />
  );
}

const styles = StyleSheet.create({
  center: { padding: 32, alignItems: 'center' },
  empty: { color: theme.muted, fontSize: 15, textAlign: 'center' },
});
