import type { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import { ArrowUpDown } from 'lucide-vue-next';
import { RouterLink } from 'vue-router/auto';

import DataTableActions from '@/components/admin/track/data-table-actions.vue';
import { API_URL } from '@/constants';
import type { Track } from '@/constants/types';
import { Button } from '@/components/ui/button';

export const columns: ColumnDef<Track>[] = [
  {
    accessorKey: 'id',
    header: ({ column }) => {
      return h(
        Button,
        {
          class: 'w-full',
          variant: 'ghost',
          onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
        },
        () => ['ID', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
      );
    },
    cell: ({ row }) => h('div', { class: 'text-center font-medium' }, row.getValue('id')),
  },
  {
    accessorKey: 'title',
    header: ({ column }) => {
      return h(
        Button,
        {
          variant: 'ghost',
          onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
        },
        () => ['Трек', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
      );
    },
    cell: ({ row }) => {
      const track = row.original;
      return h(
        RouterLink,
        { to: `/player/albums/${track.album.id}` },
        h('div', { class: 'flex items-center gap-x-2' }, [
          h('img', {
            class: 'w-16 h-16',
            src: track.temp_cover || API_URL + track.cover_uri,
            alt: '',
          }),
          h('span', { class: 'font-medium' }, track.title),
        ]),
      );
    },
  },
  {
    id: 'actions',
    enableHiding: false,
    header: () => h('div', { class: 'text-end' }, 'Действия'),
    cell: ({ row }) => {
      const track = row.original;

      return h('div', { class: 'flex justify-end' }, h(DataTableActions, track));
    },
  },
];
