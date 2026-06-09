import { useCallback } from 'react';
import { useFocusEffect } from '@react-navigation/native';

export function useRefreshOnFocus(load: () => void | Promise<void>) {
  useFocusEffect(
    useCallback(() => {
      load();
    }, [load]),
  );
}
