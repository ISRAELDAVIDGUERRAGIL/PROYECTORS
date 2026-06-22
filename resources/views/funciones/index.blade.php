<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Funciones de Cargo
        </h2>
    </x-slot>

    <div x-data="crudFunciones" x-init="cargar()">
        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex gap-4 mb-4 flex-wrap items-end">
                <button @click="abrirForm()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    + Nueva Funcion
                </button>

                <div>
                    <x-input-label for="filtro_cargo_f" value="Cargo" />
                    <select id="filtro_cargo_f" x-model="filtros.id_cargo" @change="cargar()"
                        class="mt-1 block border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        <option value="">Todos</option>
                        <template x-for="c in cargosLista" :key="c.id_cargo">
                            <option :value="c.id_cargo" x-text="c.nombre_cargo"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <x-input-label for="filtro_estado_f" value="Estado" />
                    <select id="filtro_estado_f" x-model="filtros.estado" @change="cargar()"
                        class="mt-1 block border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cargo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Descripcion</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="f in lista" :key="f.id_funcion">
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="f.id_funcion"></td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400" x-text="f.cargo?.nombre_cargo"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="f.descripcion_funcion"></td>
                                <td class="px-4 py-3 text-sm">
                                    <span x-text="f.estado"
                                        :class="f.estado === 'activo' ? 'text-green-600 bg-green-100 dark:bg-green-900 dark:text-green-300 px-2 py-1 rounded-full text-xs' : 'text-red-600 bg-red-100 dark:bg-red-900 dark:text-red-300 px-2 py-1 rounded-full text-xs'"></span>
                                </td>
                                <td class="px-4 py-3 text-sm space-x-2">
                                    <button @click="editar(f)" class="text-indigo-600 dark:text-indigo-400 hover:underline">Editar</button>
                                    <button @click="eliminar(f.id_funcion)" class="text-red-600 dark:text-red-400 hover:underline">Eliminar</button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="lista.length === 0">
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No hay funciones registradas.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @include('funciones.form')
    </div>

    <script type="module">
        import { crearClienteAPI } from '{{ Vite::asset('resources/js/api.js') }}';
        const api = crearClienteAPI();

        document.addEventListener('alpine:init', () => {
            Alpine.data('crudFunciones', () => ({
                lista: [],
                cargosLista: [],
                editando: null,
                filtros: { id_cargo: '', estado: '' },
                form: { id_cargo: '', descripcion_funcion: '', estado: 'activo' },

                async cargar() {
                    const params = new URLSearchParams();
                    if (this.filtros.id_cargo) params.set('id_cargo', this.filtros.id_cargo);
                    if (this.filtros.estado) params.set('estado', this.filtros.estado);
                    const qs = params.toString() ? '?' + params.toString() : '';
                    const [funcData, cargData] = await Promise.all([
                        api.listar('funciones-cargo', qs),
                        api.listar('cargos'),
                    ]);
                    this.lista = funcData.data;
                    this.cargosLista = cargData.data;
                },

                abrirForm() {
                    this.editando = null;
                    this.form = { id_cargo: '', descripcion_funcion: '', estado: 'activo' };
                    this.$dispatch('open-modal');
                },

                editar(f) {
                    this.editando = f.id_funcion;
                    this.form = {
                        id_cargo: f.id_cargo,
                        descripcion_funcion: f.descripcion_funcion,
                        estado: f.estado,
                    };
                    this.$dispatch('open-modal');
                },

                async guardar() {
                    if (this.editando) {
                        await api.actualizar('funciones-cargo', this.editando, this.form);
                    } else {
                        await api.crear('funciones-cargo', this.form);
                    }
                    this.$dispatch('close-modal');
                    await this.cargar();
                },

                async eliminar(id) {
                    if (!confirm('Eliminar esta funcion?')) return;
                    await api.eliminar('funciones-cargo', id);
                    await this.cargar();
                },
            }));
        });
    </script>
</x-app-layout>
