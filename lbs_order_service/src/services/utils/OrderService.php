<?php

namespace lbs\order\services\utils;

use lbs\order\models\Item;
use lbs\order\models\Order;

final class OrderService
{
    public static function getOrdersWithoutPage()
    {
        $query = Order::select('id', 'mail as client_mail', 'nom as client_nom', 'created_at as order_date', 'livraison as delivery_date', 'montant as total_amount', 'status');
        return $query->get()->toArray();
    }

    public static function getOrders($name, $sort, $page, $size)
    {

        $query = Order::select('id', 'mail as client_mail', 'nom as client_nom', 'created_at as order_date', 'livraison as delivery_date', 'montant as total_amount', 'status');

        $lastPage = ceil(count($query->get()->toArray()) / $size);

        if ($page <= 0) {
            $page = 1;
        }

        if ($page > $lastPage) {
            $page = $lastPage;
        }

        if ($name) {
            $query->where('nom', '=', $name);
        }
        if ($sort === 'date') {
            $query->orderByDesc('created_at');
        }
        if ($sort === 'amount') {
            $query->orderByDesc('montant');
        }
        if ($page !== null) {
            $query->forPage($page, $size);
        }

        return $query->get()->toArray();
    }

    public static function getOrderById(string $id, mixed $embed)
    {
        $query = Order::select('id', 'mail as client_mail', 'nom as client_nom', 'created_at as order_date', 'livraison as delivery_date', 'montant as total_amount')->where('id', '=', $id);

        if ($embed === 'items') {
            $query->with('items');
        }
        return $query->get()->toArray();
    }

    public static function update(string $id, array $data)
    {
        try {
            $order = Order::find($id);
            $order->nom = filter_var($data["client_nom"], FILTER_SANITIZE_SPECIAL_CHARS);
            $order->mail = filter_var($data["client_mail"], FILTER_SANITIZE_EMAIL);
            $order->created_at = filter_var($data["order_date"], FILTER_SANITIZE_SPECIAL_CHARS);
            $order->livraison = filter_var($data["delivery_date"], FILTER_SANITIZE_SPECIAL_CHARS);

            return $order->save();
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }

    public static function getOrderItems(string $command_id)
    {
        return Item::select('id', 'uri', 'libelle', 'tarif', 'quantite')->where('command_id', '=', $command_id)->get()->toArray();
    }
}