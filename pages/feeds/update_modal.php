<!-- update_modal.php -->
<div id="update-review-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Update Review</h3>
            <form id="update-review-form" class="mt-2">
                <input type="hidden" id="update-review-id" name="review_id">
                <textarea id="update-review-text" name="review_text" class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none" rows="4" required></textarea>
                <button type="submit" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update</button>
                <button type="button" onclick="closeUpdateModal()" class="mt-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Cancel</button>
            </form>
        </div>
    </div>
</div>