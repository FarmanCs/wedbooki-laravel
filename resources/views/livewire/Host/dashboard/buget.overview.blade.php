<div class="bg-white p-6 rounded-xl shadow">
    <h3 class="text-lg font-semibold mb-4">Budget Overview</h3>

    <div class="space-y-2">
        <p>Total Budget: £{{ number_format($budgetInfo['total_budget'], 2) }}</p>
        <p>Spent: £{{ number_format($budgetInfo['spent_amount'], 2) }}</p>
        <p>Remaining: £{{ number_format($budgetInfo['remaining_budget'], 2) }}</p>
    </div>

    <div class="mt-4">
        <div class="w-full bg-gray-200 rounded-full h-3">
            <div class="h-3 rounded-full bg-indigo-600"
                 style="width: {{ $budgetInfo['percentage_spent'] }}%">
            </div>
        </div>
    </div>
</div>
