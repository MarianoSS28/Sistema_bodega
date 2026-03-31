<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ticket;
use App\Models\TicketMensaje;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TicketsComponent extends Component
{
    use WithPagination;

    // Nuevo ticket
    public string $asunto    = '';
    public string $mensaje   = '';
    public string $prioridad = 'normal';
    public bool   $mostrarFormulario = false;

    // Hilo abierto
    public ?int   $ticketAbierto = null;
    public string $respuesta     = '';

    // Filtros (para admin)
    public string $filtroEstado = '';

    public function esAdmin(): bool
    {
        // Ajusta el id_rol de tu admin según tu BD
        return (int) Auth::user()->id_rol === 1;
    }

    protected function rulesNuevo(): array
    {
        return [
            'asunto'    => 'required|min:5|max:255',
            'mensaje'   => 'required|min:10',
            'prioridad' => 'required|in:normal,alta,urgente',
        ];
    }

    public function abrirFormulario(): void
    {
        $this->resetErrorBag();
        $this->reset(['asunto', 'mensaje', 'prioridad']);
        $this->prioridad = 'normal';
        $this->mostrarFormulario = true;
    }

    public function crearTicket(): void
    {
        $this->validate($this->rulesNuevo());

        $user = Auth::user();
        $result = DB::select('EXEC bodega.sp_registrar_ticket @id_comercio=?, @id_usuario=?, @asunto=?, @mensaje=?, @prioridad=?, @actor=?', [
            $user->id_comercio,
            $user->id,
            $this->asunto,
            $this->mensaje,
            $this->prioridad,
            $user->nombre_completo,
        ]);

        $this->mostrarFormulario = false;
        $this->reset(['asunto', 'mensaje', 'prioridad']);

        // Abrir el ticket recién creado
        if (!empty($result)) {
            $this->ticketAbierto = $result[0]->id_ticket;
        }

        session()->flash('ok', 'Ticket creado correctamente.');
    }

    public function abrirTicket(int $id): void
    {
        $this->ticketAbierto = ($this->ticketAbierto === $id) ? null : $id;
        $this->respuesta = '';
    }

    public function responder(): void
    {
        $this->validate(['respuesta' => 'required|min:3']);

        $user = Auth::user();
        DB::statement('EXEC bodega.sp_responder_ticket @id_ticket=?, @id_usuario=?, @mensaje=?, @actor=?', [
            $this->ticketAbierto,
            $user->id,
            $this->respuesta,
            $user->nombre_completo,
        ]);

        $this->respuesta = '';
        session()->flash('ok_respuesta', 'Respuesta enviada.');
    }

    public function cerrarTicket(int $id): void
    {
        DB::statement('EXEC bodega.sp_cerrar_ticket @id_ticket=?, @actor=?', [
            $id, Auth::user()->nombre_completo,
        ]);
        if ($this->ticketAbierto === $id) {
            $this->ticketAbierto = null;
        }
        session()->flash('ok', 'Ticket cerrado.');
    }

    public function updatedFiltroEstado(): void
    {
        $this->resetPage();
    }

    private function getTickets()
    {
        $user = Auth::user();

        $query = Ticket::with(['usuario', 'comercio'])
            ->where('bodega.tickets.estado', '>', 0);

        if ($this->esAdmin()) {
            // Admin ve todos; puede filtrar por estado
            if ($this->filtroEstado !== '') {
                $query->where('bodega.tickets.estado', $this->filtroEstado);
            }
        } else {
            // Usuario normal solo ve los suyos
            $query->where('id_usuario', $user->id)
                  ->where('id_comercio', $user->id_comercio);
        }

        return $query->orderByDesc('fecha_creacion')->paginate(15);
    }

    private function getMensajes(): array
    {
        if (!$this->ticketAbierto) return [];

        return DB::select('EXEC bodega.sp_mensajes_ticket @id_ticket=?', [$this->ticketAbierto]);
    }

    public function render()
    {
        $tickets  = $this->getTickets();
        $mensajes = $this->getMensajes();

        $ticketSeleccionado = $this->ticketAbierto
            ? Ticket::with(['usuario', 'comercio'])->find($this->ticketAbierto)
            : null;

        return view('livewire.tickets-component', compact('tickets', 'mensajes', 'ticketSeleccionado'))
            ->layout('layouts.app');
    }
}