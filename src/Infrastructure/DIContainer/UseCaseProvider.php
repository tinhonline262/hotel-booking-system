<?php

namespace App\Infrastructure\DIContainer;

//  Import tất cả các UseCase cần thiết
use App\Application\UseCases\CreateRoomTypeUseCase;
use App\Application\UseCases\DeleteRoomTypeUseCase;
use App\Application\UseCases\FilterRoomTypesByCapacityUseCase;
use App\Application\UseCases\FilterRoomTypesByPriceRangeUseCase;
use App\Application\UseCases\GetAllRoomTypesUseCase;
use App\Application\UseCases\GetRoomTypeUseCase;
use App\Application\UseCases\UpdateRoomTypeUseCase;
use App\Application\Validators\RoomTypeValidator;
use App\Core\Container\Container;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;

/**
 * UseCaseProvider - Nơi đăng ký (bind) tất cả các UseCase vào DI Container
 */
class UseCaseProvider
{
    /**
     * Hàm khởi tạo: gọi các nhóm UseCase để đăng ký
     */
    public static function register(Container $container): void
    {
        // Nhóm UseCase liên quan đến RoomType
        self::registerRoomTypeUseCases($container);

        // Có thể thêm các nhóm UseCase khác sau này
        // self::registerUserUseCases($container);
        // self::registerBookingUseCases($container);
    }

    /**
     * Đăng ký các UseCase thuộc nhóm RoomType
     */
    private static function registerRoomTypeUseCases(Container $container): void
    {
        // Tạo loại phòng mới
        $container->bind(CreateRoomTypeUseCase::class, function (Container $c) {
            return new CreateRoomTypeUseCase(
                $c->make(RoomTypeRepositoryInterface::class),
                $c->make(RoomTypeValidator::class)
            );
        });

        // Lấy thông tin chi tiết 1 loại phòng
        $container->bind(GetRoomTypeUseCase::class, function (Container $c) {
            return new GetRoomTypeUseCase(
                $c->make(RoomTypeRepositoryInterface::class)
            );
        });

        // Lấy danh sách tất cả loại phòng
        $container->bind(GetAllRoomTypesUseCase::class, function (Container $c) {
            return new GetAllRoomTypesUseCase(
                $c->make(RoomTypeRepositoryInterface::class)
            );
        });

        // Cập nhật loại phòng
        $container->bind(UpdateRoomTypeUseCase::class, function (Container $c) {
            return new UpdateRoomTypeUseCase(
                $c->make(RoomTypeRepositoryInterface::class),
                $c->make(RoomTypeValidator::class)
            );
        });

        // Xóa loại phòng
        $container->bind(DeleteRoomTypeUseCase::class, function (Container $c) {
            return new DeleteRoomTypeUseCase(
                $c->make(RoomTypeRepositoryInterface::class)
            );
        });

        // Lọc loại phòng theo sức chứa
        $container->bind(FilterRoomTypesByCapacityUseCase::class, function (Container $c) {
            return new FilterRoomTypesByCapacityUseCase(
                $c->make(RoomTypeRepositoryInterface::class)
            );
        });

        // Lọc loại phòng theo khoảng giá
        $container->bind(FilterRoomTypesByPriceRangeUseCase::class, function (Container $c) {
            return new FilterRoomTypesByPriceRangeUseCase(
                $c->make(RoomTypeRepositoryInterface::class)
            );
        });

        // Lấy danh sách loại phòng nổi bật (mới thêm)
        // Lấy danh sách loại phòng nổi bật - tạm thời chưa đăng ký vì lớp UseCase chưa tồn tại
    }
}