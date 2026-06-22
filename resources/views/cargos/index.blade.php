<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Cargos
        </h2>
    </x-slot>

    <div x-data="crudCargos" x-init="cargar()">
        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <button @click="abrirForm()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium mb-4">
                + Nuevo Cargo
            </button>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Descripcion</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="c in lista" :key="c.id_cargo">
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="c.id_cargo"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="c.nombre_cargo"></td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400" x-text="c.descripcion"></td>
                                <td class="px-4 py-3 text-sm space-x-2">
                                    <button @click="editar(c)" class="text-indigo-600 dark:text-indigo-400 hover:underline">Editar</button>
                                    <button @click="eliminar(c.id_cargo)" class="text-red-600 dark:text-red-400 hover:underline">Eliminar</button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="lista.length === 0">
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No hay cargos registrados.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @include('cargos.form')
    </div>

    <script type="module">
        import { crearClienteAPI } from '{{ Vite::asset('resources/js/api.js') }}';
        const api = crearClienteAPI();

        document.addEventListener('alpine:init', () => {
            Alpine.data('crudCargos', () => ({
                lista: [],
                editando: null,
                form: { nombre_cargo: '', descripcion: '' },

                async cargar() {
                    const data = await api.listar('cargos');
                    this.lista = data.data;
                },

                abrirForm() {
                    this.editando = null;
                    this.form = { nombre_cargo: '', descripcion: '' };
                    this.$dispatch('open-modal');
                },

                editar(c) {
                    this.editando = c.id_cargo;
                    this.form = { nombre_cargo: c.nombre_cargo, descripcion: c.descripcion };
                    this.$dispatch('open-modal');
                },

                async guardar() {
                    if (this.editando) {
                        await api.actualizar('cargos', this.editando, this.form);
                    } else {
                        await api.crear('cargos', this.form);
                    }
                    this.$dispatch('close-modal');
                    await this.cargar();
                },

                async eliminar(id) {
                    if (!confirm('Eliminar este cargo?')) return;
                    await api.eliminar('cargos', id);
                    await this.cargar();
                },
            }));
        });
    </script>
</x-app-layout>
