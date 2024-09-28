<?php
// feed/report_modal.php
?>
<!-- Report Modal -->
<div id="report-modal-container" class="fixed inset-0 hidden z-50">
    <!-- Background Overlay -->
    <div class="fixed inset-0 bg-black opacity-50"></div>

    <!-- Modal Content -->
    <div class="flex items-center justify-center h-full">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow-lg w-96 relative z-10">
            <h3 class="text-lg font-semibold mb-4">Report <span id="report-type"></span></h3>
            <form id="report-form" action="" method="POST">
                <input type="hidden" name="reported_id" id="reported-id">
                <input type="hidden" name="report_type" id="report-type-input">

                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason for reporting</label>
                    <select name="reason" id="reason" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 rounded-md shadow-sm focus:outline-none" onchange="toggleOtherReason()">
                        <option value="Spam">Spam</option>
                        <option value="Hate Speech">Hate Speech</option>
                        <option value="Harassment">Harassment</option>
                        <option value="Inappropriate Content">Inappropriate Content</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Other reason input -->
                <div id="other-reason-container" class="mb-4 hidden">
                    <label for="other-reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Please specify your reason</label>
                    <input type="text" name="other_reason" id="other-reason" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 rounded-md shadow-sm focus:outline-none" placeholder="Enter your reason here">
                </div>

                <div class="flex justify-end">
                    <button type="button" onclick="closeReportModal()" class="px-4 py-2 bg-gray-200 dark:bg-slate-600 rounded-md">Cancel</button>
                    <button type="submit" class="ml-2 px-4 py-2 bg-red-500 text-white rounded-md">Report</button>
                </div>
            </form>
        </div>
    </div>
</div>