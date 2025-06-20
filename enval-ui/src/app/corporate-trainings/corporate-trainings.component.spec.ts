import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CorporateTrainingsComponent } from './corporate-trainings.component';

describe('CorporateTrainingsComponent', () => {
  let component: CorporateTrainingsComponent;
  let fixture: ComponentFixture<CorporateTrainingsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CorporateTrainingsComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(CorporateTrainingsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
